<?php

namespace Database\Factories;

use App\Constants\ItemStatus;
use App\Repositories\ConditionRepository;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Itemモデルのテスト・シーディング用ファクトリ
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'brand' => null,
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(500, 3000),
            'item_image' => 'dummy.jpg',
            'user_id' => User::factory(),
            'condition_id' => ConditionRepository::getIdByCode('good'),
            'item_status' => ItemStatus::ON_SALE,
        ];
    }
}
