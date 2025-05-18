<?php

namespace Tests\Feature\Order;

use App\Constants\PaymentMethodConstants;
use Database\Seeders\ConditionSeeder;
use Database\Seeders\PaymentMethodSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers\AuthTestHelper;
use Tests\TestHelpers\ItemTestHelper;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;
    use AuthTestHelper;
    use ItemTestHelper;

    /**
     * 小計画面で変更が即時反映される
     */
    public function test_payment_method_selection_appears_correctly()
    {
        $this->seed(ConditionSeeder::class);
        $this->seed(PaymentMethodSeeder::class);

        // ログインユーザーと商品データを作成
        $user = $this->loginUser();
        $item = $this->createItem();

        // セレクトボックスの表示確認
        $response = $this->actingAs($user)->get(route('purchase.show', ['item' => $item->id]));
        $response->assertStatus(200)
            ->assertSee(PaymentMethodConstants::label(PaymentMethodConstants::CREDIT_CARD))
            ->assertSee(PaymentMethodConstants::label(PaymentMethodConstants::CONVENIENCE_STORE));

        // hiddenフィールドに、選択された支払い方法の value が反映されているか（フォーム送信用データになってるか）
        $response->assertSee('id="hidden_payment_method"', false)
            ->assertSee('value="' . PaymentMethodConstants::CREDIT_CARD . '"', false);
    }

    /* 【手動確認】
        支払い方法を選択したときに、選択結果が右側の小計欄に即時反映される（JS動作）は、ブラウザで手動確認 */
}
