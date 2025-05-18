<?php

namespace Tests\TestHelpers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait AuthTestHelper
{
  // ログインユーザー作成
  public function loginUser(array $override = []): User
  {
    $credentials = array_merge([
      'email' => 'test@example.com',
      'password' => 'password1234',
    ], $override);

    $user = User::factory()->create([
      'email' => $credentials['email'],
      'password' => Hash::make($credentials['password']),
    ]);

    $this->actingAs($user);

    return $user;
  }

  // ゲストユーザー作成
  public function createUser(array $attributes = []): User
  {
    return User::factory()->create($attributes);
  }
}
