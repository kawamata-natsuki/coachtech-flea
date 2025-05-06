<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\ConditionSeeder;
use Database\Seeders\ItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionSeeder::class);
    }

    public function test_items_can_be_filtered_by_partical_keyword()
    //「商品名」で部分一致検索ができる
    {
        // 商品を複数作成
        Item::factory()->create([
            'name' => 'AppleWatch',
        ]);
        Item::factory()->create([
            'name' => 'ApplePen',
        ]);
        Item::factory()->create([
            'name' => 'iPhone',
        ]);

        // 商品一覧ページを表示
        $response = $this->get('/');
        $response->assertStatus(200);

        // 1. 検索欄にキーワードを入力「Apple」で検索
        // 2. 検索ボタンを押す
        $response = $this->get('/?keyword=apple');
        $response->assertStatus(200);

        // 部分一致する商品が表示される
        $response->assertSee('AppleWatch');
        $response->assertSee('ApplePen');

        // 他の商品は表示されない
        $response->assertDontSee('iPhone');
    }

    public function test_keyword_search_filter_is_applied_in_mylist_tab()
    // 検索状態がマイリストでも保持されている
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user  */
        $user = User::factory()->create();

        // 商品を複数作成
        // いいね登録
        $iphone = Item::factory()->create([
            'name' => 'iPhone',
        ]);
        $applewatch = Item::factory()->create([
            'name' => 'AppleWatch'
        ]);

        $user->favoriteItems()->attach($iphone->id);

        // ユーザーにログインをする
        $this->actingAs($user);

        // 商品一覧ページを表示
        $response = $this->get('/');
        $response->assertStatus(200);

        // 1. ホームページで商品を検索 
        $response = $this->get('/?keyword=phone');
        $response->assertStatus(200);

        // 2. 検索結果が表示される 
        $response->assertSee('iPhone');
        $response->assertDontSee('AppleWatch');

        // 3. マイリストページに遷移
        $response = $this->get('/?page=mylist&keyword=phone');
        $response->assertStatus(200);

        // 検索キーワードが保持されている(検索結果が表示される)
        $response->assertSee('iPhone');
        $response->assertDontSee('AppleWatch');
    }
}
