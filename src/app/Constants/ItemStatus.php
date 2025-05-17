<?php

namespace App\Constants;

class ItemStatus
{
  public const ON_SALE = 'on_sale';
  public const SOLD_OUT = 'sold_out';

  public const LABELS = [
    self::ON_SALE => '出品中',
    self::SOLD_OUT => '売り切れ',
  ];

  // ステータスコードに対応する日本語ラベルを返す
  public static function label(string $status): string
  {
    return self::LABELS[$status] ?? '';
  }

  // 定義されているすべてのステータスコード一覧を返す
  public static function all(): array
  {
    return array_keys(self::LABELS);
  }
}