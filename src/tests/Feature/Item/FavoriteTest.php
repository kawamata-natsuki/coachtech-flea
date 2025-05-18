<?php

namespace Tests\Feature;

use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\AuthTestHelper;
use Tests\TestHelpers\ItemTestHelper;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;
    use AuthTestHelper;
    use ItemTestHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionSeeder::class);
        $this->seed(CategorySeeder::class);
    }

    /**
     * いいねアイコンを押下することによって、いいねした商品として登録することができる。
     */
    public function test_user_can_like_an_item()
    // 
    {
        // ログインユーザーと商品データを作成
        $user = $this->loginUser();
        $item = $this->createItem();

        // 2. 商品詳細ページを開く
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 3. いいねアイコンを押下
        $response = $this->post(route('item.favorite.toggle', ['item' => $item->id]));
        $response->assertRedirect();

        // いいねした商品として登録され、いいね合計値が増加表示される
        $this->assertDatabaseHas('item_favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいねの数が1であることを確認
        $response = $this->get(route('items.show', $item->id));
        $this->assertEquals(1, $item->fresh()->favorites()->count());
        $response->assertSeeText('1');
    }

    public function test_like_icon_changes_after_liking()
    // 追加済みのアイコンは色が変化する
    {
        // ログインユーザーと商品データを作成
        $user = $this->loginUser();
        $item = $this->createItem();

        // 商品詳細ページを開く
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // いいねアイコンを押下
        $response = $this->post(route('item.favorite.toggle', ['item' => $item->id]));
        $response->assertRedirect();

        // いいねした商品として登録される
        $this->assertDatabaseHas('item_favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // ユーザーをリフレッシュして、リレーションを最新化
        // 再度商品詳細ページを開いて確認
        $user->refresh();
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // いいねアイコンが押下された状態では色が変化する
        $response->assertSee('images/icons/liked.svg');
    }

    public function test_user_can_unlike_an_item()
    // 再度いいねアイコンを押下することによって、いいねを解除することができる。
    {
        // ログインユーザーと商品データを作成
        $user = $this->loginUser();
        $item = $this->createItem();

        // 商品詳細ページを開く 
        $response = $this->get(route('items.show', ['item' => $item->id]));

        // いいねアイコンを押す
        $response = $this->post(route('item.favorite.toggle', ['item' => $item->id]));
        $response->assertRedirect();

        // いいねした商品として登録される
        $this->assertDatabaseHas('item_favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // ユーザーをリフレッシュして、リレーションを最新化
        // 再度商品詳細ページを開く
        $user->refresh();
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // いいねの数が1であることを確認
        $response->assertSee('images/icons/liked.svg');
        $this->assertEquals(1, $item->fresh()->favorites()->count());
        $response->assertSeeText('1');

        // 再度いいねアイコンを押下
        $response = $this->post(route('item.favorite.toggle', ['item' => $item->id]));
        $response->assertRedirect();

        // ユーザーをリフレッシュして、リレーションを最新化
        // 再度商品詳細ページを開いて確認
        $user->refresh();
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // いいねが解除され、いいね合計値が減少表示される
        $response->assertSee('images/icons/like.svg');
        $this->assertEquals(0, $item->fresh()->favorites()->count());
        $response->assertSeeText('0');
    }
}
