@extends('layouts.app')

@section('content')
<div class="purchase-success">
  <h1 class="purchase-success__title">ご購入ありがとうございます！</h1>
  <p class="purchase-success__message">
    商品のご購入が完了しました。<br>
    発送までしばらくお待ちください。
  </p>

  <div class="purchase-success__actions">
    <a href="{{ route('items.index') }}" class="btn btn-primary">トップページへ戻る</a>
    <a href="{{ route('profile.index') }}" class="btn btn-secondary">購入履歴を見る</a>
  </div>
</div>
@endsection