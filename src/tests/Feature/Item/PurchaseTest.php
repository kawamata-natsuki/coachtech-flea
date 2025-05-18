<?php

namespace Tests\Feature;

use App\Constants\PaymentMethodConstants;
use App\Models\PaymentMethod;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\AuthTestHelper;
use Tests\TestHelpers\ItemTestHelper;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;
    use AuthTestHelper;
    use ItemTestHelper;

    protected string $paymentMethodCode;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(PaymentMethodSeeder::class);

        // テスト用の支払方法を登録
        $this->paymentMethodCode = PaymentMethodConstants::CREDIT_CARD;
    }

    /**
     * 「購入する」ボタンを押下すると購入が完了する
     */
    public function test_user_can_purchase_item_successfully()
    {
        // ログインユーザーと商品データを作成
        $user = $this->loginUser();
        $item = $this->createItem();

        // 商品購入画面を開く 
        $response = $this->get(route('purchase.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 商品を選択して「購入する」ボタンを押下
        $response = $this
            ->withSession(['purchase.payment_method' => $this->paymentMethodCode])
            ->get(route('purchase.success', ['item' => $item->id]));
        $response->assertStatus(200);

        // 購入が完了する
        $this->assertDatabaseHas('orders', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment_method_id' => PaymentMethod::where('code', $this->paymentMethodCode)->value('id'),
            'shipping_postal_code' => $user->postal_code,
            'shipping_address' => $user->address,
            'shipping_building' => $user->building,
        ]);
    }

    /**
     * 購入した商品は商品一覧画面にて「sold」と表示される
     */
    public function test_purchased_item_is_displayed_as_sold_on_item_list()
    {
        // ログインユーザーと商品データを作成
        $user = $this->loginUser();
        $item = $this->createItem();

        // 商品購入画面を開く
        $response = $this->get(route('purchase.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 商品を選択して「購入する」ボタンを押下
        $response = $this
            ->withSession(['purchase.payment_method' => $this->paymentMethodCode])
            ->get(route('purchase.success', ['item' => $item->id]));
        $response->assertStatus(200);

        // 購入が完了する
        $this->assertDatabaseHas('orders', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment_method_id' => PaymentMethod::where('code', $this->paymentMethodCode)->value('id'),
            'shipping_postal_code' => $user->postal_code,
            'shipping_address' => $user->address,
            'shipping_building' => $user->building,
        ]);

        // 商品一覧画面を表示する
        $response = $this->get('/');
        $response->assertStatus(200);

        // 購入した商品が「sold」として表示されている
        $response->assertSee('item-card__sold-label');
    }

    /**
     * 「プロフィール/購入した商品一覧」に追加されている
     */
    public function test_purchased_item_is_listed_in_user_profile_purchases()
    {
        // ログインユーザーと商品データを作成
        $user = $this->loginUser();
        $item = $this->createItem();

        // 商品購入画面を開く 
        $response = $this->get(route('purchase.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 商品を選択して「購入する」ボタンを押下
        $response = $this
            ->withSession(['purchase.payment_method' => $this->paymentMethodCode])
            ->get(route('purchase.success', ['item' => $item->id]));
        $response->assertStatus(200);

        // 購入が完了する
        $this->assertDatabaseHas('orders', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment_method_id' => PaymentMethod::where('code', $this->paymentMethodCode)->value('id'),
            'shipping_postal_code' => $user->postal_code,
            'shipping_address' => $user->address,
            'shipping_building' => $user->building,
        ]);

        // プロフィール画面を表示する
        $response = $this->get(route('profile.index', ['page' => 'buy']));
        $response->assertStatus(200);

        // 購入した商品がプロフィールの購入した商品一覧に追加されている
        $response->assertSee($item->name);
    }
}
