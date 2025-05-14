<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected string $paymentMethodCode;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(ConditionSeeder::class);

        $this->paymentMethodCode = 'credit_card';
        PaymentMethod::create([
            'code' => 'credit_card',
            'name' => 'カード支払い'
        ]);
    }

    public function test_user_profile_displays_required_informations()
    // 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'name' => 'KAWAMATA',
            'profile_image' => 'profile_images/custom.jpg',
            'postal_code' => '130-0001',
            'address' => '東京都墨田区吾妻橋1-23-1',
            'building' => 'アサヒビール1F',
        ]);

        // 出品商品を作成
        $sellingItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'sellingItem',
            'item_image' => 'sellingDummy.jpg',
        ]);

        // 購入商品を作成
        $purchasedItem = Item::factory()->create([
            'name' => 'purchasedItem',
            'item_image' => 'purchasedDummy.jpg',
        ]);
        $user->orders()->create([
            'item_id' => $purchasedItem->id,
            'user_id' => $user->id,
            'payment_method_id' => PaymentMethod::where('code', $this->paymentMethodCode)->value('id'),
            'shipping_postal_code' => '130-0001',
            'shipping_address' => '東京都墨田区吾妻橋1-23-1',
            'shipping_building' => 'アサヒビール1F',
        ]);

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. プロフィールページを開く
        $response = $this->get(route('profile.index'));
        $response->assertStatus(200);

        // プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧が正しく表示される
        // デフォルト（出品商品タブ）を確認
        $response->assertSee('storage/profile_images/custom.jpg');
        $response->assertSee('KAWAMATA');
        $response->assertSee('sellingItem');
        $response->assertSee('storage/sellingDummy.jpg');
        $response->assertDontSee('purchasedItem');

        // 購入商品タブを確認
        $response = $this->get(route('profile.index', ['page' => 'buy']));
        $response->assertStatus(200);
        $response->assertSee('storage/profile_images/custom.jpg');
        $response->assertSee('KAWAMATA');
        $response->assertSee('purchasedItem');
        $response->assertSee('storage/purchasedDummy.jpg');
        $response->assertDontSee('sellingItem');
    }
}
