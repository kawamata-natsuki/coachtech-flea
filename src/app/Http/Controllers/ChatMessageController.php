<?php

namespace App\Http\Controllers;

use App\Constants\OrderStatusConstants;
use App\Http\Requests\ChatMessageRequest;
use App\Models\ChatMessage;
use App\Models\Order;

class ChatMessageController extends Controller
{
    public function index(Order $order)
    {
        $user = auth()->user();

        // 取引中の注文（サイドバー用）
        $tradingOrders = $user->orders()
            ->where('order_status', '!=', OrderStatusConstants::COMPLETED)
            ->with('item')
            ->get();

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
        $chatPartner = auth()->id() === $order->user_id ? $order->item->user : $order->user;

        return view('chat.index', [
            'order'    => $order,
            'buyer'    => $order->user,
            'seller'   => $order->item->user,
            'messages' => $order->chatMessages,
            'tradingOrders'  => $tradingOrders,
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

        $order->chatMessages()->create($data);

        return back()->with('success', 'メッセージを送信しました。');
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

        return back()->with('success', 'メッセージを編集しました。');
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
