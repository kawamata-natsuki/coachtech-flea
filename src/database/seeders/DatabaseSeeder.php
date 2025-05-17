<?php

namespace Database\Seeders;

use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Database\Seeders\ItemSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ConditionSeeder::class,
            PaymentMethodSeeder::class,
            ItemSeeder::class,
        ]);
    }
}
