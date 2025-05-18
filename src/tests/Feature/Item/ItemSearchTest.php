<?php

namespace Tests\Feature;

use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\AuthTestHelper;
use Tests\TestHelpers\ItemTestHelper;

class ItemSearchTest extends TestCase
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
     * 「商品名」で部分一致検索ができる
     */
    public function test_items_can_be_filtered_by_partial_keyword()
    {
        // 商品を複数作成
        $this->createItem([
            'name' => 'AppleWatch',
        ]);
        $this->createItem([
            'name' => 'ApplePen',
        ]);
        $this->createItem([
            'name' => 'iPhone',
        ]);

        // 商品一覧ページを表示
        $response = $this->get('/');
        $response->assertStatus(200);

        // 検索欄にキーワード「Apple」を入力して検索する
        $response = $this->get('/?keyword=apple');
        $response->assertStatus(200);

        // AppleWatch と ApplePen が表示されている（部分一致）
        $response->assertSee('AppleWatch');
        $response->assertSee('ApplePen');

        // iPhone は表示されない
        $response->assertDontSee('iPhone');
    }

    /**
     * 検索状態がマイリストでも保持されている
     */
    public function test_keyword_search_filter_is_applied_in_mylist_tab()
    {
        // ログインユーザーといいね済の商品を複数作成
        $user = $this->loginUser();
        $iphone = $this->createItem([
            'name' => 'iPhone',
        ]);
        $applewatch = $this->createItem([
            'name' => 'AppleWatch'
        ]);
        $user->favoriteItems()->attach($iphone->id);

        // 商品一覧ページを表示
        $response = $this->get('/');
        $response->assertStatus(200);

        // 検索欄にキーワード「phone」を入力して検索する 
        $response = $this->get('/?keyword=phone');
        $response->assertStatus(200);

        // iPhone は表示、AppleWatch は非表示
        $response->assertSee('iPhone');
        $response->assertDontSee('AppleWatch');

        // マイリストページに遷移
        $response = $this->get('/?page=mylist&keyword=phone');
        $response->assertStatus(200);

        // 検索キーワードが保持されている(iPhoneのみ)
        $response->assertSee('iPhone');
        $response->assertDontSee('AppleWatch');
    }
}
