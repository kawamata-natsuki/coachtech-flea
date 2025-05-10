<?php

namespace Tests\Feature;

use App\Constants\CategoryConstants;
use App\Constants\ConditionConstants;
use App\Models\Item;
use App\Models\ItemComment;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionSeeder::class);
        $this->seed(CategorySeeder::class);
    }

    public function test_item_detail_displays_required_information()
    // 必要な情報が表示される（商品画像、商品名、ブランド名、価格、いいね数、コメント数、商品説明、商品情報（カテゴリ、商品の状態）、コメント数、コメントしたユーザー情報、コメント内容）
    {
        // ユーザーを複数作成
        /** @var \App\Models\User $user */
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // 商品データを作成
        $item = Item::factory()->create([
            'item_image' => 'dummy.jpg',
            'name' => 'test',
            'brand' => 'BRAND',
            'price' => 1000,
            'description' => 'This is test item.',
            'user_id' => $user1->id,
            'condition_id' => ConditionConstants::codeToId(ConditionConstants::GOOD),
        ]);

        $categoryIds = CategoryConstants::codesToIds([CategoryConstants::BOOK]);
        $item->categories()->attach($categoryIds);

        // 作成した商品にいいねを追加（3ユーザー）
        $item->favorites()->attach($user1->id);
        $item->favorites()->attach($user2->id);
        $item->favorites()->attach($user3->id);

        // 作成した商品にコメントを追加(3件)
        $comments = [
            'Nice item!',
            'Looks great!',
            'Is is still available?',
            'HELLO!',
        ];
        foreach ($comments as $content) {
            ItemComment::create([
                'item_id' => $item->id,
                'user_id' => $user1->id,
                'content' => $content,
            ]);
        }

        // 1. 商品詳細ページを開く
        $response = $this->get(route('items.show', $item->id));
        $response->assertStatus(200);

        // すべての情報が商品詳細ページに表示されている
        $response->assertSee('storage/dummy.jpg');
        $response->assertSee('test');
        $response->assertSee('BRAND');
        $response->assertSee('1,000');
        $response->assertSeeText('3'); // いいねの数
        $response->assertSeeText('4'); // コメントの数
        $response->assertSee('This is test item');
        $response->assertSee(CategoryConstants::label(CategoryConstants::BOOK));
        $response->assertSee(ConditionConstants::label(ConditionConstants::GOOD));
        $response->assertSee('(4)'); // コメントの数
        $response->assertSee('images/default-profile.svg'); // コメントしたユーザーのプロフィール画像
        $response->assertSee($user1->name); // コメントしたユーザー名
        $response->assertDontSee($user2->name); // コメントしていないユーザー
        $response->assertDontSee($user3->name); // コメントしていないユーザー
        $response->assertSee('Nice item!');
        $response->assertSee('Looks great!');
        $response->assertSee('Is is still available?');
        $response->assertSee('HELLO!');
    }

    public function test_multiple_categories_are_displayed_in_item_detail()
    // 複数選択されたカテゴリが表示されているか
    {
        // ユーザー作成
        $user = User::factory()->create();

        // 商品データ作成
        $item = Item::factory()->create();

        // 複数カテゴリを紐づけ
        $categoryCodes = [CategoryConstants::BOOK, CategoryConstants::GAME];
        $categoryIds = CategoryConstants::codesToIds($categoryCodes);
        $item->categories()->attach($categoryIds);

        // 1. 商品詳細ページを開く
        $response = $this->get(route('items.show', $item->id));
        $response->assertStatus(200);

        // 複数選択されたカテゴリが商品詳細ページに表示されている
        foreach ($categoryCodes as $code) {
            $response->assertSee(CategoryConstants::label($code));
        }
    }
}
