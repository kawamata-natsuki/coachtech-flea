<?php

namespace Tests\Feature;

use App\Constants\ItemStatus;
use App\Models\User;
use App\Models\Item;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionSeeder::class);
    }

    public function test_only_favorited_items_are_displayed_in_mylist_tab()
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 商品を複数作成
        $itemLiked = Item::factory()->create([
            'name' => 'LikedItem',
        ]);
        $itemNotLiked = Item::factory()->create([
            'name' => 'NotLikedItem',
        ]);

        // いいね登録
        $user->favoriteItems()->attach($itemLiked->id);

        // 1. ユーザーにログインをする
        $this->actingAs($user);

        // 2. マイリストページを開く
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);

        // いいねした商品だけが表示される
        $response->assertSee('LikedItem');
        $response->assertDontSee('NotLikedItem');
    }

    public function test_sold_label_is_displayed_for_sold_items_in_my_list_tab()
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 売り切れ商品を作成
        $soldItem = Item::factory()->create([
            'name' => 'testA',
            'item_status' => ItemStatus::SOLD_OUT,
        ]);

        // いいね登録
        $user->favoriteItems()->attach($soldItem->id);

        // 1. ユーザーにログインをする
        $this->actingAs($user);

        // 2. マイリストページを開く
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);

        // 3. 購入済み商品を確認する
        $response->assertSee('testA');

        // 購入済み商品は「Sold」と表示される
        $response->assertSee('item-card__sold-label');
    }

    public function test_items_created_by_user_are_not_displayed_in_mylist_tab()
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // ログインユーザーが出品した商品を作成
        // いいね登録
        $myItem = Item::factory()->create([
            'name' => 'MyItem',
            'user_id' => $user->id,
        ]);

        $user->favoriteItems()->attach($myItem->id);

        // ログイン＆アクセス
        $this->actingAs($user);
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);

        // 自分が出品した商品は表示されない
        $response->assertDontSee('MyItem');
    }

    public function test_no_items_are_displayed_in_mylist_tab_when_not_authenticated()
    {
        // 未認証の場合は何も表示されない

        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);
        $response->assertSee('表示する商品がありません');
    }
}
