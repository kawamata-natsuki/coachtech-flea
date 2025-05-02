@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-form">
  <div class="login-form__container">
    <h1 class="login-form__heading content__heading">
      ログイン
    </h1>

    <div class="login-form__content">
      <form class="login-form__form" action="/login" method="post" novalidate>
        @csrf
        <!-- バリデーションエラー（ログイン失敗） -->
        <div class="login-form__group">
          <x-error-message field="login" class="error-message {{ $errors->has('login') ? 'has-error' : 'no-error' }}" />
        </div>

        <!-- メールアドレス -->
        <div class="login-form__group">
          <label class="login-form__label form__label" for="email">メールアドレス</label>
          <input class="login-form__input form__input" type="email" name="email" id="email" value="{{ old('email') }}"
            placeholder="例：user@example.com">
        </div>
        <x-error-message field="email" class="error-message {{ $errors->has('email') ? 'has-error' : 'no-error' }}" />


        <!-- パスワード -->
        <div class="login-form__group">
          <label class="login-form__label form__label" for="password">パスワード</label>
          <input class="login-form__input form__input" type="password" name="password" id="password"
            placeholder="8文字以上のパスワードを入力">
        </div>
        <x-error-message field="password"
          class="error-message {{ $errors->has('password') ? 'has-error' : 'no-error' }}" />


        <!-- 送信ボタン -->
        <div class="login-form__button">
          <button class="login-form__button-submit" type="submit">ログインする</button>
        </div>
      </form>
      <div class="login-form__link">
        <a href="/register" class="login-form__link--register">会員登録はこちら</a>
      </div>
    </div>
  </div>
</div>
@endsection