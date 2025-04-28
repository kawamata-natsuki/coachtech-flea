<?php

namespace Database\Seeders;

use App\Models\Condition;
use App\Constants\Condition as ConditionConstant;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    public function run()
    {
        foreach (ConditionConstant::LABELS as $code => $label) {
            Condition::updateOrCreate(
                ['code' => $code],
                ['name' => $label]
            );
        }
    }
}
