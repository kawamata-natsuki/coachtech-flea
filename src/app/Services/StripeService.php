<?php

namespace App\Services;

use App\Models\Item;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeService
{
    // 指定された商品と支払い方法に基づいて、Stripeの支払いセッションを作成
    // 作成されたセッションURLを返す
    public function createCheckoutSession(Item $item, string $paymentMethod): string
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // 支払い方法コードに応じてStripeの決済タイプを判定
        $methodType = match ($paymentMethod) {
            'credit_card' => 'card',
            'convenience_store' => 'konbini',
            default => throw new \InvalidArgumentException('不正な支払い方法です'),
        };

        // StripeのCheckoutセッションを作成
        $session = Session::create([
            'payment_method_types' => [$methodType],
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

        return $session->url;
    }
}
