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

        // 商品詳細ページを開いて、コメントを送信、元の商品詳細ページに戻る
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);
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

        $this->assertEquals(1, $item->fresh()->comments()->count());
    }

    /**
     * ログイン前のユーザーはコメントを送信できない
     */
    public function test_guest_user_cannot_post_comment()
    {
        // 商品データを作成
        $item = $this->createItem();

        // 商品詳細ページを開いて、コメントを送信
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);
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

        // 商品詳細ページを開いて、コメントを入力しないで送信ボタンを押す
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);
        $response = $this->post(route('items.comments.store', ['item' => $item->id]), [
            'content' => '',
        ]);
        $response->assertSessionHasErrors('content');

        // バリデーションメッセージが表示される
        $errors = session('errors');
        $this->assertEquals('コメントを入力してください', $errors->first('content'));
    }

    /**
     * コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function test_validation_error_when_comment_exceeds_max_length()
    {
        // ログインユーザーと商品データを作成
        $user = $this->loginUser();
        $item = $this->createItem();

        // 商品詳細ページを開いて、256文字以上のコメントを入力して送信する
        $response = $this->get(route('items.show', ['item' => $item->id]));
        $response->assertStatus(200);
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
