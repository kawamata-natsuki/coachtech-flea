<?php

namespace Tests\TestHelpers;

use App\Models\Item;

trait ItemTestHelper
{
  /**
   * 商品を1件作成して返す
   *
   * @param array $attributes
   * @return \App\Models\Item
   */
  public function createItem(array $attributes = []): Item
  {
    return Item::factory()->create($attributes);
  }
}
