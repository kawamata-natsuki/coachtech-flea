<?php

namespace App\Constants;

use App\Models\Condition;

class ConditionConstants
{
  public const GOOD  = 'good';
  public const CLEAN = 'clean';
  public const USED  = 'used';
  public const BAD   = 'bad';

  public const LABELS = [
    self::GOOD  => '良好',
    self::CLEAN => '目立った傷や汚れなし',
    self::USED  => 'やや傷や汚れあり',
    self::BAD   => '状態が悪い',
  ];

  public static function label(string $code): string
  {
    return self::LABELS[$code] ?? '';
  }

  public static function all(): array
  {
    return array_keys(self::LABELS);
  }

  public static function codeToId(string $code): int
  {
    return Condition::where('code', $code)->value('id');
  }
}
