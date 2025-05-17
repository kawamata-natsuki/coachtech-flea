<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run()
    {
        // 一般ユーザー3人を作成（出品・購入用）
        User::create([
            'name'          => '赤井　マリオ',
            'email'         => 'mario@example.com',
            'password'      => Hash::make('12345678'),
            'postal_code'   => '123-4567',
            'address'       => 'キノコ王国 マンマミーヤ通り1-1',
            'building'      => 'ピーチ城 1F',
            'is_admin'      => false,
            'email_verified_at' => now(),
        ]);
        User::create([
            'name'          => '緑川　リンク',
            'email'         => 'link@example.com',
            'password'      => Hash::make('12345678'),
            'postal_code'   => '123-4567',
            'address'       => 'ハイラル王国 伝説の森3-2',
            'building'      => '',
            'is_admin'      => false,
            'email_verified_at' => now(),
        ]);
        User::create([
            'name'          => '星野　カービィ',
            'email'         => 'pupupu@example.com',
            'password'      => Hash::make('12345678'),
            'postal_code'   => '123-4567',
            'address'       => 'プププランド グリーングリーンズ1-1',
            'building'      => '木の下の家',
            'is_admin'      => false,
            'email_verified_at' => now(),
        ]);

        // 管理者ユーザーを作成
        User::create([
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin1234'),
            'postal_code' => '105-0011',
            'address' => '東京都港区芝公園4-2-8',
            'building' => '東京タワー 1F',
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);
    }
}
