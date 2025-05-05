<?php

namespace Tests\Feature;

use App\Constants\ItemStatus;
use App\Models\Item;
use App\Models\User;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionSeeder::class);
    }

    public function test_all_items_are_displayed()
    {
        // 事前に商品を複数作成
        Item::factory()->create([
            'name' => 'testA',
            'item_image' => 'dummyA.jpg',
        ]);
        Item::factory()->create([
            'name' => 'testB',
            'item_image' => 'dummyB.jpg',
        ]);

        // 1. 商品ページを開く
        $response = $this->get('/');
        $response->assertStatus(200);

        // 全商品を取得できる
        $response->assertSee('testA');
        $response->assertSee('testB');
        $response->assertSee('storage/dummyA.jpg');
        $response->assertSee('storage/dummyB.jpg');
    }

    public function test_sold_label_is_displayed_for_purchased_items()
    {
        // 事前に商品を複数作成
        Item::factory()->create([
            'name' => 'testA',
            'item_status' => ItemStatus::SOLD_OUT,
        ]);

        // 1. 商品ページを開く
        $response = $this->get('/');
        $response->assertStatus(200);

        // 2. 購入済み商品を表示する
        $response->assertSee('testA');

        // 購入済み商品は「Sold」と表示される
        $response->assertSee('item-card__sold-label');
    }

    public function test_items_created_by_logged_in_user_are_not_displayed()
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // ログインユーザーが出品した商品を作成
        Item::factory()->create([
            'name' => 'MyItem',
            'user_id' => $user->id,
        ]);

        // 他のユーザーが出品した商品を作成
        Item::factory()->create([
            'name' => 'OtherItem',
        ]);

        // 1. ユーザーにログインをする
        $this->actingAs($user);

        // 2. 商品ページを開く
        $response = $this->get('/');
        $response->assertStatus(200);

        // 自分が出品した商品は表示されない
        $response->assertDontSee('MyItem');
        $response->assertSee('OtherItem');
    }
}
