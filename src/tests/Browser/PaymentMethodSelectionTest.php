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
        $user = User::factory()->create();
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
                ->pause(500)
                ->select('payment_method', 'convenience?store')
                ->pause(500)
                ->assertSee('コンビニ支払い');
        });
    }
}
