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

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected string $paymentMethodCode;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConditionSeeder::class);
        $this->seed(CategorySeeder::class);

        $this->paymentMethodCode = 'credit_card';

        // テスト用の支払方法を登録
        PaymentMethod::create([
            'code' => 'credit_card',
            'name' => 'カード支払い',
        ]);
    }

    public function test_user_can_purchase_item_successfully()
    // 「購入する」ボタンを押下すると購入が完了する
    {
        // ログインユーザーを作成する
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 商品データを作成
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 商品購入画面を開く 
        $response = $this->get(route('purchase.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 3. 商品を選択して「購入する」ボタンを押下
        $response = $this
            ->withSession(['payment_method' => $this->paymentMethodCode])
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

    public function test_purchased_item_is_displayed_as_sold_on_item_list()
    // 購入した商品は商品一覧画面にて「sold」と表示される
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 商品データを作成
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 商品購入画面を開く
        $response = $this->get(route('purchase.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 3. 商品を選択して「購入する」ボタンを押下
        $response = $this
            ->withSession(['payment_method' => $this->paymentMethodCode])
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

        // 4. 商品一覧画面を表示する
        $response = $this->get('/');
        $response->assertStatus(200);

        // 購入した商品が「sold」として表示されている
        $response->assertSee('item-card__sold-label');
    }

    public function test_purchased_item_is_listed_in_user_profile_purchases()
    // 「プロフィール/購入した商品一覧」に追加されている
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 商品データを作成
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 商品購入画面を開く 
        $response = $this->get(route('purchase.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 3. 商品を選択して「購入する」ボタンを押下
        $response = $this
            ->withSession(['payment_method' => $this->paymentMethodCode])
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

        // 4. プロフィール画面を表示する
        $response = $this->get(route('profile.index', ['tab' => 'buy']));
        $response->assertStatus(200);

        // 購入した商品がプロフィールの購入した商品一覧に追加されている
        $response->assertSee($item->name);
    }
}
