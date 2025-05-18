<?php

namespace Tests\Feature;

use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\AuthTestHelper;
use Tests\TestHelpers\ItemTestHelper;

class CommentTest extends TestCase
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
     * ログイン済みのユーザーはコメントを送信できる
     */
    public function test_authenticated_user_can_post_comment()
    {
        // ログインユーザーと商品データを作成
        $user = $this->loginUser();
        $item = $this->createItem();

        // 商品詳細ページを開く
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // コメントを送信する
        $response = $this->post(route('items.comments.store', ['item' => $item->id]), [
            'content' => 'login user comment',
        ]);

        // DBに保存される
        $this->assertDatabaseHas('item_comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => 'login user comment',
        ]);

        // コメントが表示される
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertSee('login user comment');

        // コメント数が増加する
        $this->assertEquals(1, $item->fresh()->comments()->count());
        $response->assertSeeText('1');
    }

    /**
     * ログイン前のユーザーはコメントを送信できない
     */
    public function test_guest_user_cannot_post_comment()
    {
        // 商品データを作成
        $item = $this->createItem();

        // 商品詳細ページを開く(ゲストユーザー)   
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // コメントを送信する
        $response = $this->post(route('items.comments.store', ['item' => $item->id]), [
            'content' => 'guest user comment',
        ]);

        // コメントが送信されない
        $response->assertRedirect(route('login'));

        // DBに保存されていないことを確認
        $this->assertDatabaseMissing('item_comments', [
            'content' => 'guest user comment',
        ]);
    }

    /**
     * コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_validation_error_when_comment_is_empty()
    {
        // ログインユーザーと商品データを作成
        $user = $this->loginUser();
        $item = $this->createItem();

        // 商品詳細ページを開く
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // コメントを入力しないで送信ボタンを押す
        $response = $this->post(route('items.comments.store', ['item' => $item->id]), [
            'content' => '',
        ]);

        // バリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'content' => 'コメントを入力してください',
        ]);
    }

    /**
     * コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function test_validation_error_when_comment_exceeds_max_length()
    {
        // ログインユーザーと商品データを作成
        $user = $this->loginUser();
        $item = $this->createItem();

        // 商品詳細ページを開く
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 256文字以上のコメントを送信する
        $longComment = str_repeat('a', 256);
        $response = $this->post(route('items.comments.store', ['item' => $item->id]), [
            'content' => $longComment
        ]);

        // バリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'content' => 'コメントは255文字以内で入力してください',
        ]);
    }
}
