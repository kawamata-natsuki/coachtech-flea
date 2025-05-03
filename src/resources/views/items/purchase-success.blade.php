@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase-success.css') }}">
@endsection

@section('content')
<div class="purchase-success">
  <div class="purchase-success__container">
    <h1 class="purchase-success__title">ご購入ありがとうございます！</h1>
    <p class="purchase-success__message">
      商品のご購入が完了しました。<br>
      発送までしばらくお待ちください。
    </p>

    <div class="purchase-success__actions">
      <a href="{{ route('items.index') }}" class="purchase-success__button purchase-success__button--primary">
        トップページへ戻る
      </a>
      <a href="{{ route('profile.index', ['tab' => 'buy']) }}"
        class="purchase-success__button purchase-success__button--secondary">
        購入履歴を見る
      </a>
    </div>
  </div>
</div>
@endsection