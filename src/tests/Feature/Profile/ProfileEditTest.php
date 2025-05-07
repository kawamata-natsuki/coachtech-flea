<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile_information()
    // 変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name' => '旧ユーザー名',
            'postal_code' => '100-0000',
            'address' => '旧住所',
            'building' => '旧建物',
            'profile_image' => 'profile_images/dummy.jpg',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('profile.edit'));
        $response->assertStatus(200);

        $newData = [
            'name' => '新ユーザー名',
            'postal_code' => '123-4567',
            'address' => '新住所',
            'building' => '新建物',
            'profile_image' => UploadedFile::fake()->image('new_dummy.jpg'),
        ];

        $response = $this->put(route('profile.update'), $newData);
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $response = $this->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('新ユーザー名');
        $response->assertSee('123-4567');
        $response->assertSee('新住所');
        $response->assertSee('新建物');
        $response->assertSee($user->fresh()->profile_image);

        $this->assertDatabaseHas('users', [
            'name' => '新ユーザー名',
            'postal_code' => '123-4567',
            'address' => '新住所',
            'building' => '新建物',
            'profile_image' => $user->fresh()->profile_image,
        ]);
    }
}
