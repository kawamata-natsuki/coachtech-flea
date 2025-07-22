<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatMessageRequest;
use App\Models\ChatMessage;
use App\Models\Order;

class ChatMessageController extends Controller
{
    public function index(Order $order)
    {
        // チャットページ表示
    }

    public function store(ChatMessageRequest $request, Order $order)
    {
        // チャットメッセージ保存
    }

    public function update(ChatMessageRequest $request, ChatMessage $message)
    {
        // チャットメッセージ編集
    }

    public function destroy(ChatMessage $message)
    {
        // チャットメッセージ削除
    }
}
