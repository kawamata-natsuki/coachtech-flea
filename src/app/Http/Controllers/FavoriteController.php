<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Item $item)
    {
        $user = auth()->user();

        if ($user->favoriteItems()->where('item_id', $item->id)->exists()) {
            $user->favoriteItems()->detach($item->id); // いいね解除
        } else {
            $user->favoriteItems()->attach($item->id); // いいねする
        }

        return back();
    }
}
