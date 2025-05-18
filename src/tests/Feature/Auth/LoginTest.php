<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tests\TestHelpers\AuthTestHelper;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    use AuthTestHelper;

    /**
     * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_when_email_is_empty()
    {
        // ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // メールアドレス入力なしでログイン試行
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password1234',
        ]);

        // 「メールアドレスを入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /**
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_when_password_is_empty()
    {
        // ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // パスワード入力なしでログイン試行
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        // 「パスワードを入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /**
     * 入力情報が間違っている場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_with_invalid_credentials()
    {
        // ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        //誤った情報を入力してログイン試行
        $response = $this->post('/login', [
            'email' => 'notest@example.com',
            'password' => 'pass1234'
        ]);

        // 「ログイン情報が登録されていません」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'login' => 'ログイン情報が登録されていません'
        ]);
    }

    /**
     * 正しい情報が入力された場合、ログイン処理が実行される
     */
    public function test_login_succeeds_with_valid_credentials()
    {
        // ゲストユーザー作成
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password1234'),
        ]);

        // ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        //正しい情報を入力してログイン試行
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password1234',
        ]);

        // ログイン処理が実行される
        $this->assertAuthenticatedAs($user);

        // ログイン後のリダイレクト先を確認
        $response->assertRedirect('/');
    }
}
