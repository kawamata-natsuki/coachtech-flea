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

  // 状態コードに対応する日本語ラベルを返す
  public static function label(string $code): string
  {
    return self::LABELS[$code] ?? '';
  }

  // 定義されているすべての状態コード一覧を返す
  public static function all(): array
  {
    return array_keys(self::LABELS);
  }
}