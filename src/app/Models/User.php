<?php

namespace App\Models;

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

    // ユーザーが受けたレビューの平均値（小数点あり）
    public function averageRating()
    {
        return $this->reviewsReceived()->avg('rating');
    }
    // ユーザーが受けたレビューの平均値（四捨五入した整数）
    public function roundedRating()
    {
        $avg = $this->averageRating();
        return $avg !== null ? round($avg) : null;
    }

    // リレーション
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
