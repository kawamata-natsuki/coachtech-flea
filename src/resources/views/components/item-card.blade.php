@props(['item'])

<div class="item-card">
  <a href="{{ route('items.show', ['item' => $item->id]) }}">
    <div class="item-card__image">
      @if($item->isSoldOut())
      <span class="item-card__sold-label"></span>
      @endif
      <img class="item-card__img {{ $item->isSoldOut() ? 'item-card__img--sold' : '' }}"
        src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}">
    </div>
    <p class="item-card__name">{{ $item->name }}</p>
  </a>
</div>