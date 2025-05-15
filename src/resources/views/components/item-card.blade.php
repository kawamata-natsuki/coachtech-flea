@props(['item'])

<div class="item-card">
  <a href="{{ route('items.show', ['item' => $item->id]) }}">
    <!-- 商品画像 -->
    <x-item-image :item="$item" />

    <!-- 商品名 -->
    <p class="item-card__name">{{ $item->name }}</p>
  </a>
</div>