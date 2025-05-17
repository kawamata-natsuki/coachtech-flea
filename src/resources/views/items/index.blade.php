@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endsection

@section('content')
<h1 class="sr-only">商品一覧</h1>

<div class="items-page">
  <!-- メニュータブ -->
  <div class="items-page__menu">
    <ul class="items-page__tabs">
      <li class="items-page__tab {{ $tab==='all' ? 'is-active' : '' }}">
        <a class="items-page__link" href="/?keyword={{ request('keyword') }}">おすすめ</a>
      </li>
      <li class="items-page__tab {{ $tab==='mylist' ? 'is-active' : '' }}">
        <a class="items-page__link" href="/?page=mylist&keyword={{ request('keyword') }}">マイリスト</a>
      </li>
    </ul>
  </div>

  <!-- 商品一覧 -->
  <div class="items-page__container">
    @if (request('keyword') && mb_strlen(request('keyword')) < 2) <p class="items-page__notice">
      検索キーワードは2文字以上で入力してください。
      </p>
      @elseif ($items->isEmpty())
      <p class="items-page__empty">表示する商品がありません。</p>
      @else
      <div class="items-page__list">
        @foreach ($items as $item)
        <x-item-card :item="$item" />
        @endforeach
      </div>
      @endif
  </div>
</div>
@endsection