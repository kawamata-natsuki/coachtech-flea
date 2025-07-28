<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCompletedMail;
use App\Models\Item;
use App\Models\Order;
use App\Repositories\PaymentMethodRepository;
use App\Services\StripeService;
use App\Constants\PaymentMethodConstants;
use App\Constants\ItemStatus;
use App\Constants\OrderStatus;
use App\Http\Requests\PurchaseRequest;

class OrderController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->middleware('auth');
        $this->stripeService = $stripeService;
    }

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
        // 購入済のチェック
        if ($item->isSoldOut()) {
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

        // 支払いセッションを作成して、Stripeの支払い画面へリダイレクト
        $url = $this->stripeService->createCheckoutSession($item, $request->payment_method);

        session(['purchase.payment_method' => $request->payment_method]);

        return redirect($url);
    }

    // 商品購入完了後の処理
    public function success(Request $request, Item $item)
    {
        $user = auth()->user();

        // 売り切れチェック
        if ($item->isSoldOut()) {
            return redirect()->route('purchase.invalid', ['item' => $item->id]);
        }

        // セッションチェック
        if (!session()->has('purchase.payment_method')) {
            return redirect()->route('purchase.show', ['item' => $item->id])
                ->withErrors(['payment' => 'セッションが切れています。もう一度支払い方法を選択してください。']);
        }

        // セッションに支払い方法がなければ、エラーで購入画面に戻す
        $code = session()->pull('purchase.payment_method');
        if (!$code) {
            return redirect()->route('purchase.show', ['item' => $item->id])
                ->withErrors([
                    'payment' => '支払い方法が不明です。もう一度購入手続きを行ってください。',
                ]);
        }

        // DBに保存
        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method_id' => (new PaymentMethodRepository)->getIdByCode($code),
            'shipping_postal_code' => $user->postal_code,
            'shipping_address' => $user->address,
            'shipping_building' => $user->building,
        ]);

        // item_status を sold_out に更新
        $item->update([
            'item_status' => ItemStatus::SOLD_OUT,
        ]);

        return view('items.purchase-success');
    }

    // 購入キャンセル時の処理（Stripe画面から戻った場合）
    public function cancel(Request $request, Item $item)
    {
        return view('items.purchase-cancel', compact('item'));
    }

    // 無効な購入リクエスト時の表示処理（売り切れや重複購入など）
    public function invalid(Request $request, Item $item)
    {
        return view('items.purchase-invalid', compact('item'));
    }

    // 取引完了の処理
    public function complete(Order $order)
    {
        // 購入者しか実行できないようにチェック
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }

        // PENDINGでなければ何もしない
        if ($order->order_status !== OrderStatus::PENDING) {
            return redirect()->route('chat.index', ['order' => $order->id])
                ->with('error', '取引完了は一度だけ実行できます。');
        }

        // 取引ステータス「レビュー待ち状態」に更新
        $order->update(['order_status' => OrderStatus::COMPLETED_PENDING]);

        // 出品者へメール通知
        Mail::to($order->item->user->email)->send(new OrderCompletedMail($order));

        // レビュー未投稿なら review=1 を付与
        $alreadyReviewed = $order->reviews()
            ->where('reviewer_id', auth()->id())
            ->exists();

        // リダイレクト先を決定
        $params = ['order' => $order->id];
        if (!$alreadyReviewed) {
            $params['review'] = 1;
        }

        return redirect()->route('chat.index', $params);
    }
}
