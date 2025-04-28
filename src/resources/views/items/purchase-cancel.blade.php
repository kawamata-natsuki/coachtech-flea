@extends('layouts.app')

@section('css')

@endsection

@section('content')
<div class="cancel">
  <h1>購入手続きがキャンセルされました</h1>
  <p>ご注文は完了していません。再度お試しください。</p>
  <a href="{{ route('items.index') }}">商品一覧に戻る</a>
</div>
@endsection