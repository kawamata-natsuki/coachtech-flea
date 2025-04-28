<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Constants\PaymentMethod as PaymentMethodConst;
use App\Constants\ItemStatus;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class OrderController extends Controller
{
    public function show($item_id, Request $request)
    {
        $item = Item::findOrFail($item_id);
        $paymentMethods = PaymentMethodConst::LABELS;
        $user = auth()->user()->refresh();

        $selectedPaymentMethod = $request->query('payment_method');

        return view('items.purchase', compact('item', 'paymentMethods', 'user', 'selectedPaymentMethod'));
    }

    public function store(Request $request, $item_id)
    {
        $request->validate([
            'payment_method' => 'required|string|in:credit_card,convenience_store',
        ]);

        $item = Item::findOrFail($item_id);

        if ($item->price > 300000) {
            return redirect()->back()->withErrors([
                'item' => '30万円以上の商品は決済できません。',
            ]);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentMethodType = match ($request->payment_method) {
            'credit_card' => 'card',
            'convenience_store' => 'konbini',
        };

        $session = Session::create([
            'payment_method_types' => [$paymentMethodType],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/purchase/success/' . $item->id),
            'cancel_url' => url('/'),
        ]);

        session([
            'payment_method' => $request->payment_method,
        ]);

        return redirect($session->url);
    }

    public function success(Request $request, $item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        if (!Order::where('item_id', $item_id)->exists()) {
            Order::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'payment_method_id' => $this->getPaymentMethodId(session('payment_method')),
                'shipping_postal_code' => $user->postal_code,
                'shipping_address' => $user->address,
                'shipping_building' => $user->building,
            ]);
        }

        // item_status を sold_out に更新
        $item->update([
            'item_status' => ItemStatus::SOLD_OUT,
        ]);

        return view('items.purchase-success');
    }

    private function getPaymentMethodId(string $code): ?int
    {
        return PaymentMethod::where('code', $code)->value('id');
    }
}
