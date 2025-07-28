<?php

namespace App\Models;

use App\Constants\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'postal_code',
        'address',
        'building',
        'profile_image',
        'is_admin',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    // 画像をURLに変換（$user->profile_image_url でアクセスできる）
    public function getImageUrlAttribute()
    {
        return $this->profile_image
            ? asset('storage/' . $this->profile_image)
            : asset('images/icons/default-profile.svg');
    }

    // 取引が COMPLETED になったレビューの平均値（小数点あり）
    public function completedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id')
            ->whereHas('order', function ($q) {
                $q->where('order_status', \App\Constants\OrderStatus::COMPLETED);
            });
    }
    // ユーザーが受けたレビューの平均値（四捨五入した整数）
    public function roundedRating()
    {
        $avg = $this->completedReviews()->avg('rating');
        return $avg ? round($avg) : null;
    }

    // 購入した取引中の商品を取得
    public function buyingItems()
    {
        return $this->orders()
            ->where('order_status', '!=', OrderStatus::COMPLETED)
            ->with(['item', 'chatMessages' => fn($q) => $q->latest()])
            ->withCount([
                'chatMessages as unread_count' => fn($q) => $q
                    ->where('is_read', 0)
                    ->where('user_id', '!=', $this->id)
            ]);
    }

    // 出品した取引中の商品を取得
    public function sellingItems()
    {
        return Order::whereHas('item', fn($q) => $q->where('user_id', $this->id))
            ->where('order_status', '!=', OrderStatus::COMPLETED)
            ->with(['item', 'chatMessages' => fn($q) => $q->latest()])
            ->withCount([
                'chatMessages as unread_count' => fn($q) => $q
                    ->where('is_read', 0)
                    ->where('user_id', '!=', $this->id)
            ]);
    }

    // 取引中商品（購入＋出品）を統合（最新メッセージの時刻で降順）
    public function tradingItems()
    {
        return $this->buyingItems()->get()->merge(
            $this->sellingItems()->get()
        )->sortByDesc(
            fn($order) => optional($order->chatMessages->first())->created_at
        );
    }

    // ===== リレーション =====
    public function favoriteItems()
    {
        return $this->belongsToMany(
            Item::class,
            'item_favorites',
            'user_id',
            'item_id'
        )->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(ItemComment::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function reviewsWritten()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class, 'user_id');
    }
}
