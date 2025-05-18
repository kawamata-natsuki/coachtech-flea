<?php

namespace Tests\Feature;

use App\Constants\ItemStatus;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\AuthTestHelper;
use Tests\TestHelpers\ItemTestHelper;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;
    use AuthTestHelper;
    use ItemTestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionSeeder::class);
    }

    /**
     * 全商品を取得できる
     */
    public function test_all_items_are_displayed()
    {
        // 商品を複数作成
        $this->createItem([
            'name' => 'testA',
            'item_image' => 'dummyA.jpg',
        ]);
        $this->createItem([
            'name' => 'testB',
            'item_image' => 'dummyB.jpg',
        ]);

        // 商品ページを開く
        $response = $this->get('/');
        $response->assertStatus(200);

        // すべての商品が表示される
        $response->assertSee('testA');
        $response->assertSee('testB');
        $response->assertSee('storage/dummyA.jpg');
        $response->assertSee('storage/dummyB.jpg');
    }

    /**
     * 購入済み商品は「Sold」と表示される
     */
    public function test_sold_label_is_displayed_for_purchased_items()
    {
        // 購入済の商品を作成
        $this->createItem([
            'name' => 'testA',
            'item_status' => ItemStatus::SOLD_OUT,
        ]);

        // 商品ページを開く
        $response = $this->get('/');
        $response->assertStatus(200);

        // 購入済み商品を表示する
        $response->assertSee('testA');

        // 購入済み商品に「Sold」のラベルが表示される
        $response->assertSee('item-card__sold-label');
    }

    /**
     * 自分が出品した商品は表示されない
     */
    public function test_items_created_by_logged_in_user_are_not_displayed()
    {
        // ユーザーを2人作成（User1 = ログインユーザー、User2 = 他の出品者）
        $user1 = $this->createUser();
        $user2 = $this->createUser();

        // User1が出品した商品を作成
        $this->createItem([
            'name' => 'MyItem',
            'user_id' => $user1->id,
        ]);

        // 他のユーザーが出品した商品を作成
        $this->createItem([
            'name' => 'OtherItem',
            'user_id' => $user2->id,
        ]);

        // User1でログインして、商品ページを開く
        $this->actingAs($user1);
        $response = $this->get('/');
        $response->assertStatus(200);

        // 自分が出品した商品が一覧に表示されない
        $response->assertDontSee('MyItem');
        $response->assertSee('OtherItem');
    }
}
