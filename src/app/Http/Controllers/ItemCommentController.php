<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\ItemComment;

class ItemCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(CommentRequest $request, Item $item)
    {
        // コメントを作成して保存
        ItemComment::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        // 商品詳細画面にリダイレクト＆完了メッセージ表示
        return redirect()->route('items.show', ['item' => $item->id])
            ->with('success', 'コメントを投稿しました');
    }
}
