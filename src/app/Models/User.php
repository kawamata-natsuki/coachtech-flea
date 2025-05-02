<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime', // 認証処理で日時扱いに必要
    ];

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

    // 画像をURLに変換
    // ファイルアップロード時 $imagePath = $request->file('image')->store('items', 'public');
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->profile_image);
    }
}
