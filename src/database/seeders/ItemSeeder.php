<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use App\Constants\ConditionConstants;
use App\Constants\ItemStatus;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $conditionMap = Condition::all()->keyBy('code');
        $faker = Faker::create();

        $users = User::where('is_admin', false)->take(3)->get();
        if ($users->count() < 3) {
            $users = collect();
            for ($i = 0; $i < 3; $i++) {
                $users->push(User::factory()->create(['is_admin' => false]));
            }
        }

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
            [
                'name' => 'CARDCARDCARDCARDCARDCARDCARDCARDCARDCARD',
                'brand' => 'BRANDBRANDBRANDBRANDBRANDBRANDBRANDBRANDBRANDBRANDBRANDBRANDBRANDBRANDBRANDBRANDBRANDBRANDBRANDBRAND',
                'price' => 9999999,
                'description' => '美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！美品のトレカです！！！！！！！',
                'item_image' => 'images/items/card-item.jpg',
                'condition_code' => 'good',
            ]
        ];

        $sellerIndex = 0;
        $buyerIndex = 1;

        foreach ($itemsData as $data) {

            $seller = $users[$sellerIndex % 3];
            $buyer = $users[$buyerIndex % 3];
            $paymentMethodId = PaymentMethod::first()->id;
            $isSold = $sellerIndex < 6;

            // 状態（condition）取得
            $conditionCode = $data['condition_code'] ?? $faker->randomElement(ConditionConstants::all());
            $conditionId = $conditionMap[$conditionCode]->id;

            // 画像保存
            $filename = 'items/' . Str::uuid() . '.jpg';
            $content = file_get_contents(base_path('public/' . $data['item_image']));
            Storage::disk('public')->put($filename, $content);

            // 商品登録
            $item = Item::create([
                'name'         => $data['name'],
                'brand'        => $data['brand'] ?? null,
                'price'        => $data['price'],
                'description'  => $data['description'],
                'condition_id' => $conditionId,
                'item_image'   => $filename,
                'user_id'      => $seller->id,
                'item_status'  => $isSold ? ItemStatus::SOLD_OUT : ItemStatus::ON_SALE,
            ]);

            // カテゴリを紐付け
            $categories = isset($data['attach_all_categories']) && $data['attach_all_categories']
                ? Category::pluck('id')
                : Category::inRandomOrder()->take(rand(1, 3))->pluck('id');

            $item->categories()->attach($categories);

            if ($isSold) {
                Order::create([
                    'user_id' => $buyer->id,
                    'item_id' => $item->id,
                    'payment_method_id' => $paymentMethodId,
                    'shipping_postal_code' => '123-4567',
                    'shipping_address' => '東京都渋谷区1-1-1',
                    'shipping_building' => 'テストビル101',
                ]);
            }

            $sellerIndex++;
            $buyerIndex++;
        }
    }
}
