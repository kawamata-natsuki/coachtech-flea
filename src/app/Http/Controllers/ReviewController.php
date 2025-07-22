<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Order;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request, Order $order)
    {
        // レビュー投稿
    }
}
