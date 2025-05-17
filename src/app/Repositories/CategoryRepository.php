<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
  // カテゴリコードから categories テーブルの id を取得
  public static function getIdsByCodes(array $codes): array
  {
    return Category::whereIn('code', $codes)->pluck('id')->toArray();
  }

  // categories テーブルの id からカテゴリコードを取得
  public static function getCodeById(int $id): ?string
  {
    return Category::find($id)?->code;
  }
}