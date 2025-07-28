<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatMessageRequest;
use App\Models\ChatMessage;
use App\Models\Order;

class ChatMessageController extends Controller
{
    public function index(Order $order)
    {
        // 未ログインならばログイン画面へ遷移
        if (!auth()->check()) {
            return redirect()->guest(route('login'));
        }

        // 出品者・購入者以外はアクセス不可
        if (auth()->id() !== $order->user_id && auth()->id() !== $order->item->user_id) {
            abort(403, 'この取引にアクセスする権限がありません');
        }

        // メールリンク経由なら出品者のみ許可
        if (request()->query('from_email') && auth()->id() !== $order->item->user_id) {
            abort(403, '出品者しかアクセスできません');
        }
        if (request()->query('from_email') && auth()->id() === $order->item->user_id) {
            // 出品者が既にレビューを投稿済みならトップページへ
            $alreadyReviewed = $order->reviews()
                ->where('reviewer_id', auth()->id())
                ->exists();
            if ($alreadyReviewed) {
                return redirect()->route('items.index')
                    ->with('error', 'この取引は既にレビュー済みです。');
            }
            // まだレビューしていなければモーダルを表示
            session()->flash('showReviewModal', true);
        }

        // 取引データのロード処理
        $user = auth()->user();
        $tradingItems = $user->tradingItems();

        $order->load([
            'item',
            'user',
            'chatMessages.user'
        ]);

        // ログインユーザー以外が送信した未読メッセージを既読に更新
        $order->chatMessages()
            ->where('user_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // チャットの相手を取得
        $chatPartner = auth()->id() === $order->user_id
            ? $order->item->user
            : $order->user;

        return view('chat.index', [
            'order'    => $order,
            'buyer'    => $order->user,
            'seller'   => $order->item->user,
            'messages' => $order->chatMessages,
            'tradingItems'  => $tradingItems,
            'chatPartner' => $chatPartner,
            'item' => $order->item,
        ]);
    }

    public function store(ChatMessageRequest $request, Order $order)
    {
        $data = [
            'user_id' => auth()->id(),
            'message' => $request->input('message'),
        ];
        // 画像があれば保存
        if ($request->hasFile('chat_image')) {
            $data['chat_image'] = $request->file('chat_image')->store('chat_images', 'public');
        }

        $newMessage = $order->chatMessages()->create($data);

        // 送信したメッセージ位置に移動
        return redirect()
            ->route('chat.index', ['order' => $order->id])
            ->withFragment('message-' . $newMessage->id);
    }

    public function update(ChatMessageRequest $request, ChatMessage $message)
    {
        if ($message->user_id !== auth()->id()) {
            abort(403);
        }
        $data = [
            'message' => $request->input('message'),
        ];

        // 画像がある場合は更新
        if ($request->hasFile('chat_image')) {
            $data['chat_image'] = $request->file('chat_image')->store('chat_images', 'public');
        }

        $message->update($data);

        // アンカー付きでリダイレクト
        return redirect()
            ->route('chat.index', ['order' => $message->order_id])
            ->withFragment('message-' . $message->id);
    }

    public function destroy(ChatMessage $message)
    {
        if ($message->user_id !== auth()->id()) {
            abort(403);
        }
        $message->delete();

        return back()->with('success', 'メッセージを削除しました。');
    }
}
