<?php

namespace App\Models;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'payment_method_id',
        'shipping_postal_code',
        'shipping_address',
        'shipping_building',
        'order_status',
    ];

    // 未読チャットをカウントする処理
    public function getUnreadCountAttribute()
    {
        return $this->chatMessages()
            ->where('user_id', '!=', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    // リレーション
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'order_id');
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class, 'order_id');
    }
}
