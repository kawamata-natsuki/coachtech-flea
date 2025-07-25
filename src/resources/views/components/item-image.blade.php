@props(['item', 'unreadCount' => 0])

<div class="item-card__image">
  <!-- 商品画像 -->
  <img class="item-card__img {{ $item->isSoldOut() ? 'item-card__img--sold' : '' }}"
    src="{{ asset('storage/' . $item->item_image) }}"
    alt="{{ $item->name }}">

  <!-- 売り切れ時は中央にSOLD OUTを表示 -->
  @if ($item->isSoldOut())
  <div class="item-card__sold-overlay">SOLD OUT</div>
  @endif

  <!-- 未読メッセージバッジ（取引中タブだけ表示） -->
  @if ($unreadCount > 0)
  <span class="item-card__badge">
    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
  </span>
  @endif
</div>