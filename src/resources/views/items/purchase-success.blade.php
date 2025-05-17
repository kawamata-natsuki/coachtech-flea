@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/purchase-success.css') }}">
@endsection

@section('content')
<div class="purchase-success-page">
  <div class="purchase-success-page__container">
    <h1 class="purchase-success-page__title">ご購入ありがとうございます！</h1>
    <p class="purchase-success-page__message">
      商品のご購入が完了しました。<br>
      発送までしばらくお待ちください。
    </p>

    <div class="purchase-success-page__buttons">
      <a href="{{ route('items.index') }}" class="purchase-success-page__button purchase-success-page__button--primary">
        トップページへ戻る
      </a>
      <a href="{{ route('profile.index', ['page' => 'buy']) }}"
        class="purchase-success-page__button purchase-success-page__button--secondary">
        購入履歴を見る
      </a>
    </div>
  </div>
</div>
@endsection