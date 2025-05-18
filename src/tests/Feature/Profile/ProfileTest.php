<?php

namespace Tests\Feature;

use App\Constants\PaymentMethodConstants;
use App\Repositories\PaymentMethodRepository;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\AuthTestHelper;
use Tests\TestHelpers\ItemTestHelper;

class ProfileTest extends TestCase
{
    use RefreshDatabase;
    use AuthTestHelper;
    use ItemTestHelper;

    protected string $paymentMethodCode;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(ConditionSeeder::class);
        $this->seed(PaymentMethodSeeder::class);

        // テスト用の支払方法を登録
        $this->paymentMethodCode = PaymentMethodConstants::CREDIT_CARD;
    }

    /**
     * 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
     */
    public function test_user_profile_displays_required_informations()
    {
        // 初期データを持つユーザーを作成し、ログインする
        $user = $this->createUser([
            'name' => 'KAWAMATA',
            'profile_image' => 'profile_images/custom.jpg',
            'postal_code' => '130-0001',
            'address' => '東京都墨田区吾妻橋1-23-1',
            'building' => 'アサヒビール1F',
        ]);
        $this->actingAs($user);

        // 出品商品を作成
        $sellingItem = $this->createItem([
            'user_id' => $user->id,
            'name' => 'sellingItem',
            'item_image' => 'sellingDummy.jpg',
        ]);

        // 購入商品を作成
        $purchasedItem = $this->createItem([
            'name' => 'purchasedItem',
            'item_image' => 'purchasedDummy.jpg',
        ]);

        // 購入処理
        $user->orders()->create([
            'item_id' => $purchasedItem->id,
            'user_id' => $user->id,
            'payment_method_id' => PaymentMethodRepository::getIdByCode($this->paymentMethodCode),
            'shipping_postal_code' => '130-0001',
            'shipping_address' => '東京都墨田区吾妻橋1-23-1',
            'shipping_building' => 'アサヒビール1F',
        ]);

        // プロフィールページを開く
        $response = $this->get(route('profile.index'));
        $response->assertStatus(200);

        // プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧が正しく表示される
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
