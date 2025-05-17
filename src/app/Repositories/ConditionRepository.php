<?php

namespace App\Repositories;

use App\Models\Condition;

class ConditionRepository
{
  public static function getIdByCode(string $code): ?int
  {
    return Condition::where('code', $code)->value('id');
  }

  public static function getCpdeById(int $id): ?string
  {
    return Condition::fing($id)->code;
  }
}
