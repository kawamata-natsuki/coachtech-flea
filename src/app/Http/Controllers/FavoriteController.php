<?php

namespace App\Http\Controllers;

use App\Models\Item;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ログインユーザーのいいね状態をトグル（登録 or 削除）
    public function toggle(Item $item)
    {
        auth()->user()->favoriteItems()->toggle($item->id);
        return back();
    }
}
