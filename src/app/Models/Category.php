<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // リレーション
    public function items()
    {
        return $this->belongsToMany(
            Item::class,
            'category_item',
            'category_id',
            'item_id'
        )->withTimestamps();
    }
}