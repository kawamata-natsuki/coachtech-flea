<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\ItemComment;
use Illuminate\Http\Request;

class ItemCommentController extends Controller
{

    public function store(CommentRequest $request, Item $item)
    {
        ItemComment::create([
            'item_id' => $item->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return redirect()->route('items.show', ['item' => $item->id])
            ->with('success', 'コメントを投稿しました');
    }
}