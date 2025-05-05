<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout_successfully()
    {
        // 事前にログイン用のユーザーを作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password1234'),
        ]);

        // 1. ユーザーにログインをする
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password1234',
        ]);
        $this->assertAuthenticatedAs($user);

        // 2. ログアウトボタンを押す
        $response = $this->post('/logout');
        $response->assertRedirect('/');

        $this->assertGuest();
    }
}
