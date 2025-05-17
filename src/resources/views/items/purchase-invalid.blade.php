@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/purchase-invalid.css') }}">
@endsection

@section('content')
<div class="purchase-invalid-page">
  <div class="purchase-invalid-page__container">
    <h1 class="purchase-invalid-page__title">購入手続きがキャンセルされました</h1>
    <p class="purchase-invalid-page__message">
      この商品はすでに購入済み、または売り切れのため注文できません。<br>
      他の商品をご覧いただくか、トップページにお戻りください。
    </p>

    <div class="purchase-invalid-page__button">
      <a href="{{ route('items.index') }}" class="purchase-invalid-page__button--primary">
        商品一覧に戻る
      </a>
    </div>
  </div>
</div>
@endsection