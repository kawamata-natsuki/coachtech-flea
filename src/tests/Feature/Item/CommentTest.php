<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionSeeder::class);
        $this->seed(CategorySeeder::class);
    }

    public function test_authenticated_user_can_post_comment()
    // ログイン済みのユーザーはコメントを送信できる
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 商品データを作成
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 商品詳細ページを開く
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 2. コメントを入力する
        // 3. コメントボタンを押す
        $response = $this->post(route('items.comments.store', ['item' => $item->id]), [
            'content' => 'login user comment',
        ]);
        $response->assertRedirect(route('items.show', ['item' => $item->id]));

        // コメントが保存され、コメント数が増加する
        $this->assertDatabaseHas('item_comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => 'login user comment',
        ]);

        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertSee('login user comment');
    }

    public function test_guest_user_cannnot_post_comment()
    // ログイン前のユーザーはコメントを送信できない
    {
        // 商品データを作成
        $item = Item::factory()->create();

        // 1. コメントを入力する
        // 2. コメントボタンを押す
        $response = $this->post(route('items.comments.store', ['item' => $item->id]), [
            'content' => 'guest user comment',
        ]);

        // コメントが送信されない
        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('item_comments', [
            'content' => 'guest user comment',
        ]);
    }

    public function test_validation_error_when_comment_is_empty()
    // コメントが入力されていない場合、バリデーションメッセージが表示される
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 商品データを作成
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 商品詳細ページを開く
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 2. コメントボタンを押す
        $response = $this->post(route('items.comments.store', ['item' => $item->id]), [
            'content' => '',
        ]);
        $response->assertSessionHasErrors('content');

        // バリデーションメッセージが表示される
        $errors = session('errors');
        $this->assertEquals('コメントを入力してください', $errors->first('content'));
    }

    public function test_validation_error_when_comment_exceeds_max_length()
    // コメントが255字以上の場合、バリデーションメッセージが表示される
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 商品データを作成
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 商品詳細ページを開く
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 2. 256文字以上のコメントを入力する
        // 3. コメントボタンを押す
        $longComment = str_repeat('a', 256);

        $response = $this->post(route('items.comments.store', ['item' => $item->id]), [
            'content' => $longComment
        ]);
        $response->assertSessionHasErrors('content');

        // バリデーションメッセージが表示される
        $errors = session('errors');
        $this->assertEquals('コメントは255文字以内で入力してください', $errors->first('content'));
    }
}
