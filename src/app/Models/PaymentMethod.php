<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    /** 支払コードから payment_methods テーブルのid取得 */
    public static function getIdByCode(string $code): ?int
    {
        return static::where('code', $code)->value('id');
    }

    /** リレーション */
    public function orders()
    {
        return $this->hasMany(Order::class, 'payment_method_id');
    }
}
