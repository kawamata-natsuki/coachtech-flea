<?php

namespace Database\Seeders;

use App\Models\Condition;
use App\Constants\ConditionConstants;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    public function run()
    {
        foreach (ConditionConstants::LABELS as $code => $label) {
            Condition::updateOrCreate(
                ['code' => $code],
                ['name' => $label]
            );
        }
    }
}
