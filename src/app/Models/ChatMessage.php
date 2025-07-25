<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'user_id',
        'message',
        'chat_image',
    ];

    // 画像をURLに変換
    public function getImageUrlAttribute()
    {
        return $this->chat_image
            ? asset('storage/' . $this->chat_image)
            : null;
    }

    // リレーション
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
