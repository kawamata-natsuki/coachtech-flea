<?php

namespace Database\Seeders;

use App\Constants\Category as CategoryConstant;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        foreach (CategoryConstant::LABELS as $code => $label) {
            Category::updateOrCreate(
                ['code' => $code],
                ['name' => $label]
            );
        }
    }
}
