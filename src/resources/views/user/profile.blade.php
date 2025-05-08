@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<h1 class="sr-only">プロフィール</h1>

<div class="profile-page">
  <div class="profile-page__container">
    <div class="profile-page__header">

      <!-- プロフィール画像 -->
      <div class="profile-page__image-wrapper">
        @if ($user->profile_image)
        <img class="profile-page__image" src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像">
        @else
        <img class="profile-page__image--default" src="{{ asset('images/default-profile.svg') }}" alt="デフォルト画像">
        @endif
      </div>

      <div class="profile-page__info-wrapper">

        <!-- ユーザー名 -->
        <div class="profile-page__info">
          <p class="profile-page__name">{{ $user->name }}</p>
        </div>

        <!-- 編集リンク -->
        <div class="profile-page__edit">
          <a class="profile-page__edit-link" href="{{ route('profile.edit') }}">プロフィールを編集</a>
        </div>
      </div>
    </div>

    <!-- メニュー -->
    <div class="profile-page__menu-wrapper">
      <div class="profile-page__menu">
        <ul class="profile-page__tabs">
          <li
            class="profile-page__tab {{ (request('page') === 'sell' || request('page') === null) ? 'is-active' : '' }}">
            <a class="profile-page__link" href="{{ route('profile.index', ['page' => 'sell']) }}">出品した商品</a>
          </li>
          <li class="profile-page__tab {{ request('page') === 'buy' ? 'is-active' : '' }}">
            <a class="profile-page__link" href="{{ route('profile.index', ['page' => 'buy']) }}">購入した商品</a>
          </li>
        </ul>
      </div>
    </div>

    <!-- 商品リスト -->
    <div class="profile-page__items">
      @if ($page === 'sell')
      @if ($sellingItems->isEmpty())
      <p class="profile-page__empty">出品した商品はありません。</p>
      @else
      <div class="profile-page__list">
        @foreach ($sellingItems as $item)
        <x-item-card :item="$item" />
        @endforeach
      </div>
      @endif

      @elseif ($page === 'buy')
      @if ($purchasedItems->isEmpty())
      <p class="profile-page__empty">購入した商品はありません。</p>
      @else
      <div class="profile-page__list">
        @foreach ($purchasedItems as $order)
        <x-item-card :item="$order->item" />
        @endforeach
      </div>
      @endif
      @endif
    </div>
  </div>
</div>

@endsection