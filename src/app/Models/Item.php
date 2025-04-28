<?php

namespace App\Models;

use App\Constants\ItemStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'price',
        'description',
        'condition_id',
        'item_image',
        'user_id',
        'item_status',
    ];

    // 売り切れの判定
    public function isSoldOut(): bool
    {
        return $this->item_status === ItemStatus::SOLD_OUT;
    }

    // リレーション
    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'category_item',
            'item_id',
            'category_id'
        )->withTimestamps();
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class, 'condition_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(
            User::class,
            'item_favorites',
            'item_id',
            'user_id'
        )->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(ItemComment::class, 'item_id');
    }

    // 画像をURLに変換
    // ファイルアップロード時 $imagePath = $request->file('image')->store('items', 'public');
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->item_image);
    }
}
