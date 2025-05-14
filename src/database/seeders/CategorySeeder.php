<?php

namespace Database\Seeders;

use App\Constants\CategoryConstants;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        foreach (CategoryConstants::LABELS as $code => $label) {
            Category::updateOrCreate(
                ['code' => $code],
                ['name' => $label]
            );
        }
    }
}
