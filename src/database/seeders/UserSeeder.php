<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run()
    {
        User::create([
            'name' => 'AAAAA aaaaa',
            'email' => 'a@a',
            'password' => Hash::make('12345678'),
            'postal_code' => '123-4567',
            'address' => 'AAAAAaaaaa',
            'building' => '!!!!!',
        ]);
        User::create([
            'name' => 'BBBBB bbbbb',
            'email' => 'b@b',
            'password' => Hash::make('12345678'),
            'postal_code' => '123-4567',
            'address' => 'BBBBBbbbbb',
            'building' => '',
        ]);
        User::create([
            'name' => 'CCCCC ccccc',
            'email' => 'c@c',
            'password' => Hash::make('12345678'),
            'postal_code' => '123-4567',
            'address' => 'CCCCCccccc',
            'building' => '/////',
        ]);
    }
}