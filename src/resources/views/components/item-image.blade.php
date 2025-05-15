@props(['item'])

<div class="item-card__image">
  <!-- 売り切れの場合はSOLDラベルを表示 -->
  @if ($item->isSoldOut())
  <span class="item-card__sold-label"></span>
  @endif

  <!-- 売り切れ時は商品画像を暗くする -->
  <img class="item-card__img {{ $item->isSoldOut() ? 'item-card__img--sold' : '' }}"
    src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}">
</div>