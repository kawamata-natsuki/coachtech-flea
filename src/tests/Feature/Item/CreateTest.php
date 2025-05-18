<?php

namespace Tests\Feature;

use App\Constants\CategoryConstants;
use App\Constants\ConditionConstants;
use App\Repositories\ConditionRepository;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tests\TestHelpers\AuthTestHelper;

/**
 * 商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、商品の説明、販売価格）
 */
class CreateTest extends TestCase
{
    use RefreshDatabase;
    use AuthTestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(ConditionSeeder::class);
    }

    public function test_user_can_register_item_information()
    {
        // ログインユーザーを作成
        $user = $this->loginUser();

        // 商品出品画面を開く
        $response = $this->get(route('items.create'));
        $response->assertStatus(200);

        // 出品商品のデータ
        $file = UploadedFile::fake()->image('dummy.jpg');
        $data = [
            'name' => 'testItem',
            'price' => 1000,
            'description' => 'This is a test item.',
            'condition_code' => ConditionConstants::GOOD,
            'item_image' => $file,
            'category_codes' => [CategoryConstants::BOOK],
        ];

        // 商品を出品する
        $response = $this->post(route('items.store'), $data);
        $response->assertRedirect(route('items.index'));
        $response->assertSessionHas('success');

        // items テーブルに保存されていることを確認
        $this->assertDatabaseHas('items', [
            'name' => 'testItem',
            'price' => 1000,
            'description' => 'This is a test item.',
            'condition_id' => ConditionRepository::getIdByCode(ConditionConstants::GOOD),
            'user_id' => $user->id,
            'item_status' => 'on_sale',
        ]);
    }
}
