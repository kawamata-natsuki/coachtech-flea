<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 名前が入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_register_fails_when_name_is_empty()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 名前を入力せずに登録ボタンを押す
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
        ]);

        // 「お名前を入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }

    /**
     * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_register_fails_when_email_is_empty()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        //メールアドレスを入力せずに登録ボタンを押す
        $response = $this->post('/register', [
            'name' => 'Tanaka Kanata',
            'email' => '',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
        ]);

        // 「メールアドレスを入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /**
     * パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_register_fails_when_password_is_empty()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // パスワードを入力せずに登録ボタンを押す
        $response = $this->post('/register', [
            'name' => 'Tanaka Kanata',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password1234',
        ]);

        // 「パスワードを入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    /**
     * パスワードが7文字以下の場合、バリデーションメッセージが表示される
     */
    public function test_register_fails_when_password_is_too_short()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 7文字以下のパスワードと他の必要項目を入力登録ボタンを押す 
        $response = $this->post('/register', [
            'name' => 'Tanaka Kanata',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        // 「パスワードは8文字以上で入力してください」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    /**
     * パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
     */
    public function test_register_fails_when_password_confirmation_does_not_match()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 確認用パスワードと異なるパスワードを入力し、他の必要項目も入力して、登録ボタンを押す 
        $response = $this->post('/register', [
            'name' => 'Tanaka Kanata',
            'email' => 'test@example.com',
            'password' => 'password1234',
            'password_confirmation' => 'pass1234',
        ]);

        // 「パスワードと一致しません」というバリデーションメッセージが表示される
        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);
    }

    /**
     * 全ての項目が入力されている場合、会員情報が登録され、プロフィール編集画面に遷移される
     */
    public function test_register_succeeds_and_redirects_to_profile_when_all_fields_are_valid()
    {
        // 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 全ての必要項目を正しく入力して、登録ボタンを押す 
        $response = $this->post('/register', [
            'name' => 'Tanaka Kaanata',
            'email' => 'test@example.com',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
        ]);

        // 会員情報が登録される
        $this->assertDatabaseHas('users', [
            'name' => 'Tanaka Kaanata',
            'email' => 'test@example.com',
        ]);

        // プロフィール編集画面に遷移する
        $response->assertRedirect(route('profile.edit'));

        // ログイン状態の確認
        $this->assertAuthenticated();
    }
}
