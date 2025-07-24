@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/profile.css') }}">
@endsection

@section('content')
<h1 class="sr-only">プロフィール</h1>

<div class="profile-page">
  <div class="profile-page__container">

    <div class="profile-page__header">
      <!-- アイコン＋名前 -->
      <x-user-icon :user="$user" wrapperClass="profile-page__user" imageClass="user-icon"
        defaultClass="user-icon--default" nameClass="profile-page__name" />
      <!-- 編集リンク -->
      <div class="profile-page__edit-wrapper">
        <a class="button--outline-red profile-page__edit-link" href="{{ route('profile.edit') }}">
          プロフィールを編集
        </a>
      </div>
    </div>

    <!-- メニュータブ -->
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
          <li class="profile-page__tab {{ request('page') === 'trading' ? 'is-active' : '' }}">
            <a class="profile-page__link" href="{{ route('profile.index', ['page' => 'trading']) }}">取引中の商品</a>
          </li>
        </ul>
      </div>
    </div>

    <!-- 商品リスト -->
    <div class="profile-page__items">
      <!-- 出品商品一覧 -->
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

      <!-- 購入商品一覧 -->
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

      <!-- 取引中の商品一覧 -->
      @elseif ($page === 'trading')
      @if ($tradingItems->isEmpty())
      <p class="profile-page__empty">取引中の商品はありません。</p>
      @else
      <div class="profile-page__list">
        @foreach ($tradingItems as $order)
        <x-item-card :item="$order->item" :link="route('chat.index', $order->id)" />
        @endforeach
      </div>
      @endif
      @endif
    </div>
  </div>
</div>
@endsection