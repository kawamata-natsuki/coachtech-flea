@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/purchase-cancel.css') }}">
@endsection

@section('content')
<div class="purchase-cancel-page">
  <div class="purchase-cancel-page__container">
    <h1 class="purchase-cancel-page__title">購入手続きがキャンセルされました</h1>
    <p class="purchase-cancel-page__message">
      ご注文は完了していません。<br>
      再度お試しいただくか、トップページにお戻りください。
    </p>

    <div class="purchase-cancel-page__button">
      <a href="{{ route('items.index') }}" class="purchase-cancel-page__button--primary">
        トップページへ戻る
      </a>
    </div>
  </div>
</div>
@endsection