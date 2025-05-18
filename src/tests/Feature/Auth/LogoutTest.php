<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\AuthTestHelper;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    use AuthTestHelper;

    /**
     * ログアウトができる
     */
    public function test_user_can_logout_successfully()
    {
        // ログインユーザー作成
        $user = $this->loginUser();

        // ログアウトする
        $response = $this->post('/logout');

        // ログアウト処理が実行される
        $this->assertGuest();

        // ログアウト後のリダイレクト先確認
        $response->assertRedirect('/');
    }
}
