@props(['item', 'link' => null])

<div class="item-card">
  <a href="{{ $link ?? route('items.show', ['item' => $item->id]) }}">
    <!-- 商品画像 -->
    <x-item-image :item="$item" />

    <!-- 商品名 -->
    <p class="item-card__name">{{ $item->name }}</p>
  </a>
</div>