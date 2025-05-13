<?php

namespace App\Http\Controllers;

use App\Models\Item;

class FavoriteController extends Controller
{
    public function toggle(Item $item)
    {
        auth()->user()->favoriteItems()->toggle($item->id);
        return back();
    }
}
