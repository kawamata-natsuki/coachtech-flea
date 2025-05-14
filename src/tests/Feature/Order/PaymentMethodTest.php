<?php

namespace Tests\Feature\Order;

use App\Models\User;
use App\Models\Item;
use App\Models\PaymentMethod;
use Database\Seeders\ConditionSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    // 小計画面で変更が即時反映される
    public function test_payment_method_selection_appears_correctly()
    {
        $this->seed(ConditionSeeder::class);
        $this->seed(PaymentMethodSeeder::class);

        /** @var \App\Models\User */
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        PaymentMethod::firstOrCreate(
            ['code' => 'convenience_store'],
            ['name' => 'コンビニ支払い']
        );

        PaymentMethod::firstOrCreate(
            ['code' => 'credit_card'],
            ['name' => 'カード支払い']
        );

        // 表示確認
        $response = $this->actingAs($user)->get(route('purchase.show', ['item' => $item->id]));

        $response->assertStatus(200)
            ->assertSee('カード支払い')
            ->assertSee('コンビニ払い');

        // hiddenフィールドに、選択された支払い方法の value が反映されているか（フォーム送信用データになってるか）
        $response->assertSee('id="hidden_payment_method"', false)
            ->assertSee('value="credit_card"', false);

        /* 【手動確認】
        支払い方法を選択したときに、選択結果が右側の小計欄に即時反映される（JS動作）は、ブラウザで手動確認 */
    }
}
