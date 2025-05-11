@php
use App\Constants\ConditionConstants;
@endphp


@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="item-detail">
  <div class="item-detail__container">
    <div class="item-detail__wrapper">
      <!-- 左：画像 -->
      <div class="item-detail__image">
        <div class="item-card__image">
          @if ($item->isSoldOut())
          <span class="item-card__sold-label"></span>
          @endif
          <img class="item-card__img {{ $item->isSoldOut() ? 'item-card__img--sold' : '' }}"
            src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}">
        </div>
      </div>

      <!-- 右：テキスト情報 -->
      <div class="item-detail__info">
        <div class="item-detail-main">
          <h1 class="item-detail__name">{{ $item->name }}</h1>
          @if (!empty($item->brand))
          <p class="item-detail__brand">{{ $item->brand }}</p>
          @endif
          <p class="item-detail__price">
            <span class="item-detail__price-sub">¥</span>
            {{ number_format($item->price) }}
            <span class="item-detail__price-sub">(税込)</span>
          </p>
        </div>

        <div class="item-detail__actions">
          <!-- いいねカウント -->
          <div class="item-detail__favorite">
            <form action="{{ route('item.favorite.toggle', ['item' => $item->id]) }}" method="post">
              @csrf
              <button class="favorite-button" type="submit">
                @if (auth()->check() && auth()->user()->favoriteItems->contains($item))
                <img class="favorite-count__icon" src="{{ asset('images/liked.svg') }}" alt="いいね済み">
                @else
                <img class="favorite-count__icon" src="{{ asset('images/like.svg') }}" alt="いいね">
                @endif
              </button>
            </form>
            <p class="favorite-count">{{ $item->favorites->count() }}</p>
          </div>

          <!-- コメントカウント -->
          <div class="item-comment__count">
            <img class="comment-count__icon" src="{{ asset('images/comment.svg') }}" alt="コメント数">
            <p class="comment-count">{{ $item->comments->count() }}</p>
          </div>
        </div>

        <!-- 購入ボタン -->
        <div class="item-purchase__button">
          @if (!$item->isSoldOut())
          <a class="purchase-button" href="{{ route('purchase.show', ['item' => $item->id]) }}">
            購入手続きへ
          </a>
          @else
          <span class="purchase-button is-disabled">SOLD OUT</span>
          @endif
        </div>

        <div class="item-description">
          <h2 class="item-description__heading">商品説明</h2>
          <p class="item-description__text">{{ $item->description }}</p>
        </div>

        <!-- 商品の情報・カテゴリ -->
        <div class="item-info">
          <h2 class="item-info__heading">商品の情報</h2>
          <div class="item-info__category">
            <p class="item-info__label">カテゴリー</p>
            <div class="item-info__tags">
              @foreach ($item->categories as $category)
              <span class="item-category">{{ $category->name }}</span>
              @endforeach
            </div>
          </div>
          <div class="item-info__status">
            <p class="item-info__label">商品の状態</p>
            <p class="item-condition">{{ ConditionConstants::label(optional($item->condition)->code) }}</p>
          </div>
        </div>

        <!-- コメント表示 -->
        <div class="item-comment__form">
          <h2 class="item-comment__heading">コメント({{ $item->comments->count() }})</h2>
          @foreach ($item->comments as $comment)
          <div class="comment">
            <div class="comment__header">
              @php
              $profileImage = optional($comment->user)->profile_image;

              $isDefault = empty($profileImage);
              $profileImageUrl = $isDefault
              ? asset('images/default-profile.svg')
              : asset('storage/' . $profileImage);
              $profileImageClass = $isDefault
              ? 'user-icon user-icon--default'
              : 'user-icon';
              @endphp
              <img class="{{ $profileImageClass }}" src="{{ $profileImageUrl }}" alt="プロフィール画像">
              <span class="comment__user">{{ $comment->user->name ?? '匿名ユーザー' }}</span>
            </div>

            <p class="comment__content">{!! nl2br(e($comment->content)) !!}</p>

            <div class="comment__footer">
              <span class="comment__date">{{ $comment->created_at->format('Y/m/d H:i') }}</span>
            </div>
          </div>
          @endforeach
        </div>

        <!-- コメントフォーム -->
        <div class="comment-form">
          <h3 class="comment-form__heading">商品へのコメント</h3>
          <form action="{{ route('items.comments.store', ['item' => $item -> id]) }}" method="post">
            @csrf
            <textarea class="comment-form__textarea" name="comment" id="comment">{{ old('comment') }}</textarea>
            <x-error-message class="error-message" field="comment" />
            <button class="comment-button" type="submit">コメントを送信する</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  @endsection