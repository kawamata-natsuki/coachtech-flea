<?php

namespace Database\Seeders;

use App\Constants\PaymentMethod as PaymentMethodConstant;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        foreach (PaymentMethodConstant::LABELS as $code => $label) {
            PaymentMethod::updateOrCreate(
                ['code' => $code],
                ['name' => $label]
            );
        }
    }
}
