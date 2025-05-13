@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase-cancel.css') }}">
@endsection

@section('content')
<div class="purchase-cancel">
  <div class="purchase-cancel__container">
    <h1 class="purchase-cancel__title">購入手続きがキャンセルされました</h1>
    <p class="purchase-cancel__message">
      ご注文は完了していません。<br>
      再度お試しいただくか、トップページにお戻りください。
    </p>

    <div class="purchase-cancel__actions">
      <a href="{{ route('items.index') }}" class="purchase-cancel__button purchase-cancel__button--primary">
        商品一覧に戻る
      </a>
    </div>
  </div>
</div>
@endsection