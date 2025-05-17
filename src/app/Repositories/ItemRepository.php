<?php

namespace App\Repositories;

use App\Models\Item;

class ItemRepository
{
  // おすすめタブに表示する商品を取得する
  public function getRecommendedItems(?string $keyword, ?int $userId = null)
  {
    // 自分以外の出品商品を対象に、検索ワードがあれば2文字以上の部分一致で絞り込み、いいね数→新着順で並び替え(売り切れ商品は最後に表示)
    return Item::withCount('favorites')
      ->when($userId, fn($query) => $query->where('user_id', '!=', $userId))
      ->when(mb_strlen($keyword) >= 2, fn($query) => $query->where('name', 'like', "%{$keyword}%"))
      ->orderByRaw("FIELD(item_status, 'on_sale', 'sold_out')")
      ->orderByDesc('favorites_count')
      ->orderByDesc('created_at')
      ->get();
  }

  // マイリストに表示する商品を取得する
  public function getFavoriteItems($keyword, $user)
  {
    // 自分がいいねした商品の中から、検索ワードがあれば2文字以上の部分一致で絞り込み、いいねした順に並び替え
    return $user->favoriteItems()
      ->when(mb_strlen($keyword) >= 2, fn($query) => $query->where('items.name', 'like', "%{$keyword}%"))
      ->withCount('favorites')
      ->orderBy('item_favorites.created_at', 'desc')
      ->distinct()
      ->get();
  }
}
