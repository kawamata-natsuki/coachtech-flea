<?php

namespace App\Repositories;

use App\Models\PaymentMethod;

class PaymentMethodRepository
{
  // 支払コードから payment_methods テーブルのid取得
  public static function getIdByCode(string $code): ?int
  {
    return PaymentMethod::where('code', $code)->value('id');
  }
}
