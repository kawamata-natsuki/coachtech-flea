<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\User;
use App\Models\PaymentMethod;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ConditionSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PaymentMethodSelectionTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(ConditionSeeder::class);
    }

    public function test_payment_method_selection_is_reflected_immediately()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $item = Item::factory()->create();

        PaymentMethod::create([
            'code' => 'credit_card',
            'name' => 'カード支払い'
        ]);
        PaymentMethod::create([
            'code' => 'convenience_store',
            'name' => 'コンビニ支払い',
        ]);

        $this->browse(function (Browser $browser) use ($user, $item) {
            $browser->loginAs($user)
                ->visit(route('purchase.show', ['item' => $item->id]))
                ->waitFor('select[name="payment_method"]', 5)
                ->select('select[name="payment_method"]', 'convenience_store');

            // script() は別に呼び出して実行だけする
            $browser->script('updatePaymentMethod()');

            $browser->pause(1000)
                ->assertSeeIn('#selected-method', 'コンビニ払い');
        });
    }
}
