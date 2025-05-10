<?php

namespace Database\Seeders;

use App\Constants\PaymentMethodConstants;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        foreach (PaymentMethodConstants::LABELS as $code => $label) {
            PaymentMethod::updateOrCreate(
                ['code' => $code],
                ['name' => $label]
            );
        }
    }
}
