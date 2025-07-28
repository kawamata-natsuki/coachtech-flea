<?php

namespace App\Http\Controllers;

use App\Constants\OrderStatus;
use App\Http\Requests\ReviewRequest;
use App\Models\Review;
use App\Models\Order;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request, Order $order)
    {
        $reviewerId = auth()->id();

        // 取引完了ボタンが押されていない場合はレビュー不可
        if ($order->order_status === OrderStatus::PENDING) {
            return redirect()
                ->route('chat.index', ['order' => $order->id])
                ->with('error', '購入者が取引完了するまでレビューできません。');
        }

        // 既にレビューが存在するか確認
        $existing = Review::where('order_id', $order->id)
            ->where('reviewer_id', $reviewerId)
            ->exists();
        if ($existing || $order->order_status === OrderStatus::COMPLETED) {
            return redirect()
                ->route('items.index')
                ->with('error', 'この取引は既にレビュー済みです。');
        }

        // reviewee_id を判定
        $revieweeId = ($reviewerId === $order->user_id)
            ? $order->item->user_id   // 購入者→出品者をレビュー
            : $order->user_id;        // 出品者→購入者をレビュー

        // レビューを保存
        Review::create([
            'order_id'      => $order->id,
            'reviewer_id'   => $reviewerId,
            'reviewee_id'   => $revieweeId,
            'rating'        => $request->input('rating'),
        ]);

        // ステータスを更新
        $reviewCount = Review::where('order_id', $order->id)->count();

        // 最初のレビューが出品者・購入者どちらでも必ず COMPLETED_PENDING
        if ($reviewCount === 1) {
            $order->update(['order_status' => OrderStatus::COMPLETED_PENDING]);
        } elseif ($reviewCount >= 2) {
            $order->update(['order_status' => OrderStatus::COMPLETED]);
        }

        return redirect()->route('items.index', ['order' => $order->id])
            ->with('success', 'レビューを投稿しました！');
    }
}
