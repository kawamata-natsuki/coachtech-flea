<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionSeeder::class);
        $this->seed(CategorySeeder::class);
    }

    public function test_user_can_like_an_item()
    // いいねアイコンを押下することによって、いいねした商品として登録することができる。
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 商品データを作成
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

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

        $response = $this->get(route('items.show', $item->id));
        $response->assertSeeText('1');
    }

    public function test_like_icon_changes_after_liking()
    // 追加済みのアイコンは色が変化する
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 商品データを作成
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 商品詳細ページを開く
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 3. いいねアイコンを押下
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
        $response->assertSee('images/liked.svg');
    }

    public function test_user_can_unlike_an_item()
    // 再度いいねアイコンを押下することによって、いいねを解除することができる。
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 商品データを作成
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 商品詳細ページを開く 
        $response = $this->get(route('items.show', ['item' => $item->id]));

        // 3. いいねアイコンを押下
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
        $response->assertSee('images/liked.svg');
        $response->assertSeeText('1');

        // 再度いいねアイコンを押下
        $response = $this->post(route('item.favorite.toggle', ['item' => $item->id]));
        $response->assertRedirect();

        // ユーザーをリフレッシュして、リレーションを最新化
        // 再度商品詳細ページを開いて確認
        // いいねが解除され、いいね合計値が減少表示される
        $user->refresh();
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('images/like.svg');
        $response->assertSeeText('0');
    }
}
