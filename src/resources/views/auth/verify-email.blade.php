@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-email">
  <h1 class="sr-only">メール認証</h1>
  <p class="verify-email__message">
    登録していただいたメールアドレスに認証メールを送付しました。<br>
    メール認証を完了してください。
  </p>

  <form class="verify-email__form verify-email__form--confirm" method="GET" action="{{ route('verification.check') }}">
    <button class="verify-email__confirm-button" type="submit">
      認証はこちらから
    </button>
  </form>

  <form class="verify-email__form verify-email__form--resend" method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button class="verify-email__resend-button" type="submit">
      認証メールを再送する
    </button>
  </form>
</div>
@endsection