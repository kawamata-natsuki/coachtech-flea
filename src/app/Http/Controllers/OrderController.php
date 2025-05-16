<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Constants\PaymentMethodConstants;
use App\Constants\ItemStatus;
use App\Http\Requests\PurchaseRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class OrderController extends Controller
{
    // 商品購入画面の表示
    public function show(Request $request, Item $item)
    {
        $paymentMethods = PaymentMethodConstants::LABELS;
        $user = auth()->user();

        $selectedPaymentMethod = $request->query('payment_method');

        return view('items.purchase', compact('item', 'paymentMethods', 'user', 'selectedPaymentMethod'));
    }

    // 商品購入の処理
    public function store(PurchaseRequest $request, Item $item)
    {
        // すでに購入済かチェック
        if (
            $item->item_status === ItemStatus::SOLD_OUT ||
            Order::where('item_id', $item->id)->exists()
        ) {
            return redirect()->route('purchase.invalid', ['item' => $item->id]);
        }

        // コンビニ支払いの価格が30万超えてたらエラーにする（Stripe仕様上の制約）
        if (
            $request->payment_method === 'convenience_store' &&
            $item->price > 300000
        ) {
            return redirect()->back()->withErrors([
                'item_price' => 'コンビニ支払いの上限は30万円です。',
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
            'success_url' => route('purchase.success', ['item' => $item->id]),
            'cancel_url' => route('purchase.cancel', ['item' => $item->id]),
        ]);

        session([
            'purchase.payment_method' => $request->payment_method,
        ]);

        return redirect($session->url);
    }

    public function success(Request $request, Item $item)
    {
        $user = auth()->user();
        $code = session('purchase.payment_method');

        if (!$code) {
            return redirect()->route('purchase.show', ['item' => $item->id])
                ->withErrors([
                    'payment' => '支払い方法が不明です。もう一度購入手続きを行ってください。',
                ]);
        }

        session()->pull('purchase.payment_method');

        if (!Order::firstWhere('item_id', $item->id)) {
            Order::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'payment_method_id' => PaymentMethod::getIdByCode($code),
                'shipping_postal_code' => $user->postal_code,
                'shipping_address' => $user->address,
                'shipping_building' => $user->building,
            ]);
        }

        // item_status を sold_out に更新
        $item->update([
            'item_status' => ItemStatus::SOLD_OUT,
        ]);

        session([
            'purchase.payment_method' => $request->payment_method,
            'purchase.complete' => true
        ]);

        return view('items.purchase-success');
    }

    public function cancel(Request $request, Item $item)
    {
        return view('items.purchase-cancel', compact('item'));
    }
}
