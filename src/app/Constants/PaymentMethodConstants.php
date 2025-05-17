<?php

namespace App\Constants;

class PaymentMethodConstants
{
    public const CONVENIENCE_STORE = 'convenience_store';
    public const CREDIT_CARD       = 'credit_card';

    public const LABELS = [
        self::CONVENIENCE_STORE => 'コンビニ払い',
        self::CREDIT_CARD       => 'カード支払い',
    ];

    // 支払いコードに対応する日本語ラベルを返す
    public static function label(string $code): string
    {
        return self::LABELS[$code] ?? '';
    }

    // 定義されているすべての支払いコード一覧を返す
    public static function all(): array
    {
        return array_keys(self::LABELS);
    }
}