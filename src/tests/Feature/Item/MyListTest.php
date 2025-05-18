<?php

namespace Tests\Feature;

use App\Constants\ItemStatus;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\AuthTestHelper;
use Tests\TestHelpers\ItemTestHelper;

class MyListTest extends TestCase
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
     * いいねした商品だけが表示される
     */
    public function test_only_favorited_items_are_displayed_in_mylist_tab()
    {
        // ログインユーザーを作成
        $user = $this->loginUser();

        // 複数商品を作成、 $itemLiked にいいねする
        $itemLiked = $this->createItem([
            'name' => 'LikedItem',
        ]);
        $itemNotLiked = $this->createItem([
            'name' => 'NotLikedItem',
        ]);
        $user->favoriteItems()->attach($itemLiked->id);

        // マイリストページを開く
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);

        // いいねをした商品が表示される
        $response->assertSee('LikedItem');
        $response->assertDontSee('NotLikedItem');
    }

    /**
     * 購入済み商品は「Sold」と表示される
     */
    public function test_sold_label_is_displayed_for_sold_items_in_my_list_tab()
    {
        // ログインユーザーを作成
        $user = $this->loginUser();

        // 売り切れ商品を作成し、いいねをする
        $soldItem = $this->createItem([
            'name' => 'testA',
            'item_status' => ItemStatus::SOLD_OUT,
        ]);
        $user->favoriteItems()->attach($soldItem->id);

        // マイリストページを開く
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);

        // 購入済み商品を確認する
        $response->assertSee('testA');

        // 購入済み商品に「Sold」のラベルが表示される
        $response->assertSee('item-card__sold-label');
    }

    /**
     * 自分が出品した商品は表示されない
     */
    public function test_items_created_by_user_are_not_displayed_in_mylist_tab()
    {
        // ログインユーザーを作成
        $user = $this->loginUser();

        // ログインユーザーが出品した商品を作成し、いいねする
        $myItem = $this->createItem([
            'name' => 'MyItem',
            'user_id' => $user->id,
        ]);
        $user->favoriteItems()->attach($myItem->id);

        // マイリストページを開く
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);

        // 自分が出品した商品が一覧に表示されない
        $response->assertDontSee('MyItem');
    }

    /**
     * 未認証の場合は何も表示されない
     */
    public function test_no_items_are_displayed_in_mylist_tab_when_not_authenticated()
    {
        // ゲストユーザー
        auth()->logout();

        // 何も表示されない
        $response = $this->get('/?page=mylist');
        $response->assertStatus(200);
        $response->assertSee('表示する商品がありません');
    }
}
