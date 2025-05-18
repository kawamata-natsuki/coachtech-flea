<?php

namespace App\Repositories;

use App\Models\Item;

class ItemRepository
{
  // おすすめタブ用の商品一覧を取得
  // - 自分以外の商品を対象にする
  // - 2文字以上の検索ワードがあれば、部分一致で絞り込み
  // - いいね数 → 新着順で並び替え（売り切れ商品は最後に表示）
  public function getRecommendedItems(?string $keyword, ?int $userId = null)
  {
    return Item::withCount('favorites')
      ->when($userId, fn($query) => $query->where('user_id', '!=', $userId))
      ->when(mb_strlen($keyword) >= 2, fn($query) => $query->where('name', 'like', "%{$keyword}%"))
      ->orderByRaw("FIELD(item_status, 'on_sale', 'sold_out')")
      ->orderByDesc('favorites_count')
      ->orderByDesc('created_at')
      ->get();
  }

  // マイリストに表示する商品を取得
  // - 自分以外の商品で「いいね」した商品を対象にする
  // - 2文字以上の検索ワードがあれば、部分一致で絞り込み
  // - いいねした日時の降順で並び替え
  public function getFavoriteItems($keyword, $user)
  {
    return $user->favoriteItems()
      ->where('items.user_id', '!=', $user->id)
      ->when(mb_strlen($keyword) >= 2, fn($query) => $query->where('items.name', 'like', "%{$keyword}%"))
      ->withCount('favorites')
      ->orderBy('item_favorites.created_at', 'desc')
      ->distinct()
      ->get();
  }
}
