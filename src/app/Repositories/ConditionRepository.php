<?php

namespace App\Repositories;

use App\Models\Condition;

class ConditionRepository
{
  // 状態コードから conditions テーブルの ID を取得
  public static function getIdByCode(string $code): ?int
  {
    return Condition::where('code', $code)->value('id');
  }

  // conditions テーブルの ID から状態コードを取得
  public static function getCodeById(int $id): ?string
  {
    return Condition::find($id)?->code;
  }
}