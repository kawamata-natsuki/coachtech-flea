<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_fails_when_email_is_empty()
    {
        // 1. ログインページを開く 
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. メールアドレスを入力せずに他の必要項目を入力する 
        // 3. ログインボタンを押す
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password1234',
        ]);
        $response->assertSessionHasErrors(['email']);

        // メールアドレスが入力されていない場合、バリデーションメッセージが表示される
        $errors = session('errors');
        $this->assertEquals('メールアドレスを入力してください', $errors->first('email'));
    }

    public function test_login_fails_when_password_is_empty()
    {
        // 1. ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. パスワードを入力せずに他の必要項目を入力する 
        // 3. ログインボタンを押す
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);
        $response->assertSessionHasErrors(['password']);

        // パスワードが入力されていない場合、バリデーションメッセージが表示される
        $errors = session('errors');
        $this->assertEquals('パスワードを入力してください', $errors->first('password'));
    }

    public function test_login_fails_with_invalid_credentials()
    {
        // 1. ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. 必要項目を登録されていない情報を入力する 
        // 3. ログインボタンを押す
        $response = $this->post('/login', [
            'email' => 'notest@example.com',
            'password' => 'pass1234'
        ]);
        $response->assertSessionHasErrors(['login']);

        // 入力情報が間違っている場合、バリデーションメッセージが表示される
        $errors = session('errors');
        $this->assertEquals('ログイン情報が登録されていません', $errors->first('login'));
    }

    public function test_login_succeeds_with_valid_credentials()
    {
        // 事前にログイン用のユーザーを作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password1234'),
        ]);

        // 1. ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. 全ての必要項目を入力する
        // 3. ログインボタンを押す
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password1234',
        ]);

        // ログイン後のリダイレクト先を確認
        $response->assertRedirect('/');

        // 正しい情報が入力された場合、ログイン処理が実行される
        $this->assertAuthenticatedAs($user);
    }
}
