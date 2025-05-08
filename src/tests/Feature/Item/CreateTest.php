<?php

namespace Tests\Feature;

use App\Constants\Category;
use App\Constants\Condition;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(ConditionSeeder::class);
    }

    public function test_user_can_register_item_information()
    // 商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、商品の説明、販売価格）
    {
        /** @var \App\Models\User $user */

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('items.create'));
        $response->assertStatus(200);

        $file = UploadedFile::fake()->image('dummy.jpg');
        $data = [
            'name' => 'testItem',
            'price' => 1000,
            'description' => 'This is a test item.',
            'condition_code' => Condition::GOOD,
            'item_image' => $file,
            'category_codes' => [Category::BOOK],
        ];

        $response = $this->post(route('items.store'), $data);
        $response->assertRedirect(route('items.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('items', [
            'name' => 'testItem',
            'price' => 1000,
            'description' => 'This is a test item.',
            'condition_id' => Condition::codeToId(Condition::GOOD),
            'user_id' => $user->id,
            'item_status' => 'on_sale',
        ]);

        $item = \App\Models\Item::where('name', 'testItem')->first();
        $this->assertNotNull($item);
        $this->assertStringContainsString('items/', $item->item_image);
        $this->assertFileExists(storage_path('app/public/' . $item->item_image));
        $this->assertEquals('on_sale', $item->item_status);
        $this->assertEquals(Condition::codeToId(Condition::GOOD), $item->condition_id);

        $bookCategoryId = Category::codesToIds([Category::BOOK])[0];
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $bookCategoryId,
        ]);
    }
}
