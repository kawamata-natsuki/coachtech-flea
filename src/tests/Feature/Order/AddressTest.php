<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    protected string $paymentMethodCode;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(ConditionSeeder::class);

        $this->paymentMethodCode = 'credit_card';

        // テスト用の支払方法を登録
        PaymentMethod::create([
            'code' => 'credit_card',
            'name' => 'カード支払い',
        ]);
    }

    public function test_registered_address_is_reflected_on_purchase_screen()
    // 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 送付先住所変更画面で住所を登録する 
        $response = $this->get(route('address.edit', ['item' => $item->id]));
        $response->assertStatus(200);

        $addressData = [
            'postal_code' => '130-0001',
            'address' => '東京都墨田区吾妻橋1-23-1',
            'building' => 'アサヒビール1F',
        ];
        $this->put(route('address.update', ['item' => $item->id]), $addressData);

        // 3. 商品購入画面を再度開く
        $response = $this->get(route('purchase.show', ['item' => $item->id]));

        // 登録した住所が商品購入画面に正しく反映される
        $response->assertSee('130-0001');
        $response->assertSee('東京都墨田区吾妻橋1-23-1');
        $response->assertSee('アサヒビール1F');
    }

    public function test_address_is_stored_with_purchased_item()
    // 購入した商品に送付先住所が紐づいて登録される
    {
        /** @var \App\Models\User */
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 1. ユーザーにログインする
        $this->actingAs($user);

        // 2. 送付先住所変更画面で住所を登録する 
        $response = $this->get(route('address.edit', ['item' => $item->id]));
        $response->assertStatus(200);

        $addressData = [
            'postal_code' => '130-0001',
            'address' => '東京都墨田区吾妻橋1-23-1',
            'building' => 'アサヒビール1F',
        ];
        $this->put(route('address.update', ['item' => $item->id]), $addressData);

        // 3. 商品を購入する
        $response = $this->get(route('purchase.show', ['item' => $item->id]));
        $response->assertStatus(200);

        $response = $this
            ->withSession(['payment_method' =>  $this->paymentMethodCode])
            ->get(route('purchase.success', ['item' => $item->id]));
        $response->assertStatus(200);

        // 正しく送付先住所が紐づいている
        $this->assertDatabaseHas('orders', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment_method_id' => PaymentMethod::where('code', $this->paymentMethodCode)->value('id'),
            'shipping_postal_code' => $user->postal_code,
            'shipping_address' => $user->address,
            'shipping_building' => $user->building,
        ]);
    }
}
