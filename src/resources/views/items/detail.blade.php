@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/detail.css') }}">
@endsection

@section('content')
<div class="item-detail-page">
  <div class="item-detail-page__container">
    <div class="item-detail-page__wrapper">

      <!-- 左側の画像エリア -->
      <div class="item-detail-page__image">
        <x-item-image :item="$item" />
      </div>

      <!-- 右側のテキスト情報エリア -->
      <div class="item-detail-page__info">
        <div class="item-detail-page__main">
          <h1 class="item-detail-page__name">{{ $item->name }}</h1>
          @if (!empty($item->brand))
          <p class="item-detail-page__brand">{{ $item->brand }}</p>
          @endif
          <p class="item-detail-page__price">
            <span class="item-detail-page__price-sub">¥</span>
            {{ number_format($item->price) }}
            <span class="item-detail-page__price-sub">(税込)</span>
          </p>
        </div>

        <!-- いいね・コメント -->
        <div class="item-detail-page__actions">
          <!-- いいねカウント -->
          <div class="item-detail-page__favorite-count">
            <form action="{{ route('item.favorite.toggle', ['item' => $item->id]) }}" method="post">
              @csrf
              <button class="favorite-button" type="submit">
                @if (auth()->check() && auth()->user()->favoriteItems->contains($item))
                <img class="favorite-count__icon" src="{{ asset('images/icons/liked.svg') }}" alt="いいね済み">
                @else
                <img class="favorite-count__icon" src="{{ asset('images/icons/like.svg') }}" alt="いいね">
                @endif
              </button>
            </form>
            <p class="favorite-count__number">{{ $item->favorites->count() }}</p>
          </div>

          <!-- コメントカウント -->
          <div class="item-detail-page__comment-count">
            <img class="comment-count__icon" src="{{ asset('images/icons/comment.svg') }}" alt="コメント数">
            <p class="comment-count__number">{{ $item->comments->count() }}</p>
          </div>
        </div>

        <!-- 購入ボタン -->
        <div class="item-detail-page__purchase-button">
          @if (!$item->isSoldOut())
          <a class="button--solid-red item-detail-page__purchase-button-submit"
            href="{{ route('purchase.show', ['item' => $item->id]) }}">
            購入手続きへ
          </a>
          @else
          <span class="is-disabled item-detail-page__purchase-button-submit">SOLD OUT</span>
          @endif
        </div>

        <!-- 商品説明 -->
        <div class="item-detail-page__description">
          <h2 class="item-detail-page__description-heading">商品説明</h2>
          <p class="item-detail-page__description-text">{{ $item->description }}</p>
        </div>

        <!-- 商品の情報・カテゴリ -->
        <div class="item-detail-page__meta">
          <h2 class="item-detail-page__meta-heading">商品の情報</h2>
          <div class="item-detail-page__meta-category">
            <p class="item-detail-page__meta-label">カテゴリー</p>
            <div class="item-detail-page__meta-tags">
              @foreach ($categoryLabels as $label)
              <span class="item-detail-page__meta-category-tag">{{ $label }}</span> @endforeach
            </div>
          </div>
          <div class="item-detail-page__meta-status">
            <p class="item-detail-page__meta-label">商品の状態</p>
            <p class="item-detail-page__meta-condition">{{ $conditionLabel }}</p>
          </div>
        </div>

        <!-- コメント表示 -->
        <div class="item-detail-page__comment-section">
          <h2 class="item-detail-page__comment-section-heading">コメント( {{ $item->comments->count() }} )</h2>
          @foreach ($item->comments as $comment)
          <div class="item-detail-page__comment">
            <div class="item-detail-page__comment-header">
              <x-user-icon :user="$comment->user" wrapperClass="item-detail-page__comment-header" imageClass="user-icon"
                defaultClass="user-icon--default" nameClass="item-detail-page__comment-user" />
            </div>

            <p class="item-detail-page__comment-content">{!! nl2br(e($comment['content'])) !!}</p>

            <div class="item-detail-page__comment-footer">
              <span class="item-detail-page__comment-date">{{ $comment->created_at->format('Y/m/d H:i') }}</span>
            </div>
          </div>
          @endforeach
        </div>

        <!-- コメントフォーム -->
        <div class="item-detail-page__comment-form">
          <h3 class="item-detail-page__comment-form-heading">商品へのコメント</h3>
          <form action="{{ route('items.comments.store', ['item' => $item -> id]) }}" method="post">
            @csrf
            <textarea class="item-detail-page__comment-form-textarea" name="content"
              id="content">{{ old('content') }}</textarea>
            <x-error-message class="error-message" field="content" />

            <button type="submit" class="button--solid-red item-detail-page__comment-submit-button">コメントを送信</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection