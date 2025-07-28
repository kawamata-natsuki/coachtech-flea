<?php

namespace App\Constants;

class OrderStatus
{
  const PENDING   = 'pending';
  const COMPLETED_PENDING = 'completed_pending';
  const COMPLETED = 'completed';

  public static function labels(): array
  {
    return [
      self::PENDING => '取引中',
      self::COMPLETED_PENDING => 'レビュー待ち',
      self::COMPLETED => '取引完了',
    ];
  }
}
