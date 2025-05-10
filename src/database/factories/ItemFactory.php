<?php

namespace Database\Factories;

use App\Constants\ConditionConstants;
use App\Constants\ItemStatus;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Factory for PHPUnit tests (Item model)
     */

    protected $model = Item::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'brand' => null,
            'description' => $this->faker->sentence(),
            'price' => 1000,
            'item_image' => 'dummy.jpg',
            'user_id' => User::factory(),
            'condition_id' => ConditionConstants::codeToId(ConditionConstants::GOOD),
            'item_status' => ItemStatus::ON_SALE,
        ];
    }
}
