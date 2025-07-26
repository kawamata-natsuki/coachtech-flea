<?php

namespace App\Constants;

class OrderStatus
{
  const PENDING   = 'pending';
  const COMPLETED = 'completed';

  public static function labels(): array
  {
    return [
      self::PENDING => '取引中',
      self::COMPLETED => '取引完了',
    ];
  }
}
