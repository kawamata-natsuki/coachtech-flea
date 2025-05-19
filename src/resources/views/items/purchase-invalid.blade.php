@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/purchase-invalid.css') }}">
@endsection

@section('content')
<div class="purchase-invalid-page">
  <div class="purchase-invalid-page__container">
    <h1 class="purchase-invalid-page__title">購入手続きが無効になりました</h1>
    <p class="purchase-invalid-page__message">
      この商品はすでに購入済み、または売り切れのため、ご注文いただけません。<br>
      他の商品を探すか、トップページにお戻りください。
    </p>

    <div class="purchase-invalid-page__button">
      <a href="{{ route('items.index') }}" class="purchase-invalid-page__button--primary">
        トップページへ戻る
      </a>
    </div>
  </div>
</div>
@endsection