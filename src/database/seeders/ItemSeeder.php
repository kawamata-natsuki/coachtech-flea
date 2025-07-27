<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use App\Constants\ItemStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $conditionMap = Condition::all()->keyBy('code');

        // ダミーユーザー3人を用意
        $users = User::where('is_admin', false)->take(3)->get();
        if ($users->count() < 3) {
            $users = collect();
            for ($i = 0; $i < 3; $i++) {
                $users->push(User::factory()->create(['is_admin' => false]));
            }
        }

        // 商品データ
        $itemsData = [
            [
                'name' => '腕時計',
                'brand' => 'EMPORIO ARMANI',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'item_image' => 'images/items/armani-mens-clock.jpg',
                'condition_code' => 'good',
            ],
            [
                'name' => 'HDD',
                'brand' => 'TOSHIBA',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'item_image' => 'images/items/hdd-hard-disk.jpg',
                'condition_code' => 'clean',
            ],
            [
                'name' => '玉ねぎ3束',
                'brand' => 'IBARAKI FARM',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'item_image' => 'images/items/onion-3-bundles.jpg',
                'condition_code' => 'used',
            ],
            [
                'name' => '革靴',
                'brand' => 'REGAL',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'item_image' => 'images/items/leather-shoes.jpg',
                'condition_code' => 'bad',
            ],
            [
                'name' => 'ノートPC',
                'brand' => 'DELL',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'item_image' => 'images/items/silver-laptop.jpg',
                'condition_code' => 'good',
            ],
            [
                'name' => 'マイク',
                'brand' => 'SONY',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'item_image' => 'images/items/studio-mic.jpg',
                'condition_code' => 'clean',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'item_image' => 'images/items/shoulder-bag.jpg',
                'condition_code' => 'used',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'item_image' => 'images/items/tumbler.jpg',
                'condition_code' => 'bad',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'item_image' => 'images/items/coffee-mill.jpg',
                'condition_code' => 'good',
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'item_image' => 'images/items/makeup-set.jpg',
                'condition_code' => 'clean',
            ],
        ];

        // 商品登録
        foreach ($itemsData as $index => $data) {
            // 出品者の割り当て
            $seller = $index < 5 ? $users[0] : $users[1];

            $conditionCode = $data['condition_code'];
            $conditionId = $conditionMap[$conditionCode]->id;

            // 画像保存
            $filename = 'items/' . Str::uuid() . '.jpg';
            $content = file_get_contents(base_path('public/' . $data['item_image']));
            Storage::disk('public')->put($filename, $content);

            Item::create([
                'name'         => $data['name'],
                'brand'        => $data['brand'] ?? null,
                'price'        => $data['price'],
                'description'  => $data['description'],
                'condition_id' => $conditionId,
                'item_image'   => $filename,
                'user_id'      => $seller->id,
                'item_status'  => ItemStatus::ON_SALE,
            ])->categories()->attach(
                Category::inRandomOrder()->take(rand(1, 3))->pluck('id')
            );
        }
    }
}
