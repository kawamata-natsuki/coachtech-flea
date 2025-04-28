<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition as ConditionModel;
use App\Constants\Condition;
use App\Constants\ItemStatus;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $conditionMap = ConditionModel::all()->keyBy('code');

        $faker = Faker::create();

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            ],
        ];

        foreach ($items as $data) {

            // ランダムなユーザーを毎回1人選ぶ
            $randomUser = User::inRandomOrder()->first();

            // ランダム状態取得
            $conditionCode = $faker->randomElement(Condition::all());
            $conditionId = $conditionMap[$conditionCode]->id;

            // 画像ファイル名
            $filename = 'items/' . Str::uuid() . '.jpg';

            // 外部画像をダウンロードして保存
            $content = file_get_contents($data['item_image']);
            Storage::disk('public')->put($filename, $content);

            // 商品登録
            // 商品データ一覧には'user_id' 'item_status'はありませんが、テーブル設計の都合で追加しています（READMEに記載する）
            $item = Item::create([
                'name'         => $data['name'],
                'price'        => $data['price'],
                'description'  => $data['description'],
                'condition_id' => $conditionId,
                'item_image'   => $filename,
                'user_id'      => $randomUser->id,
                'item_status' => 'on_sale',
            ]);

            // ランダムでカテゴリを1〜3個選んで紐付け
            $categories = \App\Models\Category::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $item->categories()->attach($categories);
        }
    }
}