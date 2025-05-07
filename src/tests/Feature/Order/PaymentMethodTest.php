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

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(ConditionSeeder::class);
    }

    public function test_payment_method_selection_is_reflected_in_view()
    // 小計画面で変更が即時反映される
    {
        // ログインユーザーを作成
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        // 商品データを作成
        $item = Item::factory()->create();

        // 支払方法を追加
        PaymentMethod::create([
            'code' => 'credit_card',
            'name' => 'カード支払',
        ]);

        // ログイン&商品購入画面へアクセス
        $this->actingAs($user);

        $response = $this->get(route('purchase.show', ['item' => $item->id]));
        $response->assertStatus(200);

        // 1. 支払い方法選択画面を開く 


    }
}
