<?php

namespace App\Constants;

class CategoryConstants
{
  public const FASHION     = 'fashion';
  public const APPLIANCE   = 'appliance';
  public const INTERIOR    = 'interior';
  public const LADIES      = 'ladies';
  public const MENS        = 'mens';
  public const COSMETICS   = 'cosmetics';
  public const BOOK        = 'book';
  public const GAME        = 'game';
  public const SPORTS      = 'sports';
  public const KITCHEN     = 'kitchen';
  public const HANDMADE    = 'handmade';
  public const ACCESSORY   = 'accessory';
  public const TOY         = 'toy';
  public const BABY_KIDS   = 'baby_kids';

  public const LABELS = [
    self::FASHION     => 'ファッション',
    self::APPLIANCE   => '家電',
    self::INTERIOR    => 'インテリア',
    self::LADIES      => 'レディース',
    self::MENS        => 'メンズ',
    self::COSMETICS   => 'コスメ',
    self::BOOK        => '本',
    self::GAME        => 'ゲーム',
    self::SPORTS      => 'スポーツ',
    self::KITCHEN     => 'キッチン',
    self::HANDMADE    => 'ハンドメイド',
    self::ACCESSORY   => 'アクセサリー',
    self::TOY         => 'おもちゃ',
    self::BABY_KIDS   => 'ベビー・キッズ',
  ];

  // カテゴリコードに対応する日本語ラベルを返す
  public static function label(string $code): string
  {
    return self::LABELS[$code] ?? '';
  }

  // 定義されているすべてのカテゴリコード一覧を返す
  public static function all(): array
  {
    return array_keys(self::LABELS);
  }
}