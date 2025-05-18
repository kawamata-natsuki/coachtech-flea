<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\AuthTestHelper;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    use AuthTestHelper;

    public function test_user_can_logout_successfully()
    {
        // ログアウトができる

        // ログインユーザー作成、ログイン後にログアウトする
        $user = $this->loginUser();

        $response = $this->post('/logout');
        $response->assertRedirect('/');

        // ログアウト処理が実行される
        $this->assertGuest();
    }
}
