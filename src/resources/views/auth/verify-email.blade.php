@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-email">
  <h1 class="sr-only">メール認証</h1>
  <p class="verify-email__message">
    登録したメールアドレスに認証メールを送信しました。
  </p>
  <p class="verify-email__message">
    メール内の「認証ボタン」をクリックして認証を完了してください。<br>
    認証後は別タブで画面が開きます。この画面は閉じても問題ありません。
  </p>

  <!-- 
認証はこちらからボタン？リンク？これはメールから認証するので不要では？ 
<a href="#">認証はこちらから</a>
-->

  <form class="verify-email__form" method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button class="verify-email__resend-button" type="submit">認証メールを再送する</button>
  </form>
</div>
@endsection