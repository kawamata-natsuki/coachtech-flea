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

    /**
     * 商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、商品の説明、販売価格）
     */
    public function test_user_can_register_item_information()
    {
        // ログインユーザーを作成
        $user = $this->loginUser();

        // 商品出品画面を開く
        $response = $this->get(route('items.create'));
        $response->assertStatus(200);

        // 出品商品のデータを準備
        $file = UploadedFile::fake()->image('dummy.jpg');
        $data = [
            'name' => 'testItem',
            'price' => 1000,
            'description' => 'This is a test item.',
            'condition_code' => ConditionConstants::GOOD,
            'item_image' => $file,
            'category_codes' => [CategoryConstants::BOOK],
        ];

        // 商品を出品、フラッシュメッセージとともに商品一覧ページへ戻る
        $response = $this->post(route('items.store'), $data);
        $response->assertRedirect(route('items.index'));
        $response->assertSessionHas('success');

        // 各項目が正しく保存されている
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
