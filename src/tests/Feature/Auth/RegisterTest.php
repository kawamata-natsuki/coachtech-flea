<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_fails_when_name_is_empty()
    {
        // 1. 会員登録ページを開く 
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 2. 名前を入力せずに他の必要項目を入力する 
        // 3. 登録ボタンを押す(POSTリクエスト)
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
        ]);
        $response->assertSessionHasErrors(['name']);

        // 名前が入力されていない場合、バリデーションメッセージが表示される
        $errors = session('errors');
        $this->assertEquals('お名前を入力してください', $errors->first('name'));
    }

    public function test_register_fails_when_email_is_empty()
    {
        // 1. 会員登録ページを開く 
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 2. メールアドレスを入力せずに他の必要項目を入力する 
        // 3. 登録ボタンを押す
        $response = $this->post('/register', [
            'name' => 'Tanaka Kanata',
            'email' => '',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
        ]);
        $response->assertSessionHasErrors(['email']);

        // メールアドレスが入力されていない場合、バリデーションメッセージが表示される
        $errors = session('errors');
        $this->assertEquals('メールアドレスを入力してください', $errors->first('email'));
    }

    public function test_register_fails_when_password_is_empty()
    {
        // 1. 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 2. パスワードを入力せずに他の必要項目を入力する 
        // 3. 登録ボタンを押す
        $response = $this->post('/register', [
            'name' => 'Tanaka Kanata',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password1234',
        ]);
        $response->assertSessionHasErrors('password');

        // パスワードが入力されていない場合、バリデーションメッセージが表示される
        $errors = session('errors');
        $this->assertEquals('パスワードを入力してください', $errors->first('password'));
    }

    public function test_register_fails_when_password_is_too_short()
    {
        // 1. 会員登録ページを開く 
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 2. 7文字以下のパスワードと他の必要項目を入力する 
        // 3. 登録ボタンを押す
        $response = $this->post('/register', [
            'name' => 'Tanaka Kanata',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);
        $response->assertSessionHasErrors('password');

        // パスワードが7文字以下の場合、バリデーションメッセージが表示される
        $errors = session('errors');
        $this->assertEquals('パスワードは8文字以上で入力してください', $errors->first('password'));
    }

    public function test_register_fails_when_password_confirmation_does_not_match()
    {
        // 1. 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 2. 確認用パスワードと異なるパスワードを入力し、他の必要項目も入力する
        // 3. 登録ボタンを押す
        $response = $this->post('/register', [
            'name' => 'Tanaka Kanata',
            'email' => 'test@example.com',
            'password' => 'password1234',
            'password_confirmation' => 'pass1234',
        ]);
        $response->assertSessionHasErrors('password');

        // パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
        $errors = session('errors');
        $this->assertEquals('パスワードと一致しません', $errors->first('password'));
    }

    public function test_register_succeeds_and_redirects_to_profile_when_all_fields_are_valid()
    {
        // 1. 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 2. 全ての必要項目を正しく入力する
        // 3. 登録ボタンを押す
        $response = $this->post('/register', [
            'name' => 'Tanaka Kaanata',
            'email' => 'test@example.com',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
        ]);

        // 全ての項目が入力されている場合、会員情報が登録され、ログイン画面に遷移される
        $this->assertDatabaseHas('users', [
            'name' => 'Tanaka Kaanata',
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/mypage/profile');

        // ログイン状態の確認
        $this->assertAuthenticated();
    }
}
