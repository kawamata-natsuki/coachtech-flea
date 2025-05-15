@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')

<div class="register-form">
  <div class="register-form__container">
    <h1 class="register-form__heading content__heading">
      会員登録
    </h1>

    <div class="register-form__content">
      <form class="register-form__form" action="/register" method="post" novalidate>
        @csrf

        <!-- ユーザー名 -->
        <div class="register-form__group">
          <label class="register-form__label form__label" for="name">ユーザー名</label>
          <input class="register-form__input form__input" type="text" name="name" id="name" value="{{ old('name') }}"
            placeholder="例：山田　太郎">
          <x-error-message field="name" class="error-message {{ $errors->has('name') ? 'has-error' : 'no-error' }}" />
        </div>

        <!-- メールアドレス -->
        <div class="register-form__group">
          <label class="register-form__label form__label" for="email">メールアドレス</label>
          <input class="register-form__input form__input" type="email" name="email" id="email"
            value="{{ old('email') }}" placeholder="例：user@example.com">
          <x-error-message field="email" class="error-message {{ $errors->has('email') ? 'has-error' : 'no-error' }}" />
        </div>

        <!-- パスワード -->
        <div class="register-form__group">
          <label class="register-form__label form__label" for="password">パスワード</label>
          <input class="register-form__input form__input" type="password" name="password" id="password"
            placeholder="8文字以上のパスワードを入力">
          <x-error-message field="password"
            class="error-message {{ $errors->has('password') ? 'has-error' : 'no-error' }}"
            :excludeMessage="'パスワードと一致しません'" />
        </div>

        <!-- 確認用パスワード -->
        <div class="register-form__group">
          <label class="register-form__label form__label" for="password_confirmation">確認用パスワード</label>
          <input class="register-form__input form__input" type="password" name="password_confirmation"
            id="password_confirmation" placeholder="もう一度パスワードを入力">
          <x-password-confirm-error class="error-message {{ $errors->has('password') ? 'has-error' : 'no-error' }}"
            passwordMessage="パスワードと一致しません" />
        </div>

        <!-- 送信ボタン -->
        <div class="register-form__button">
          <button class="register-form__button-submit" type="submit">登録する</button>
        </div>
      </form>

      <!-- リンク -->
      <div class="register-form__link">
        <a href="/login" class="register-form__link--login">ログインはこちら</a>
      </div>
    </div>
  </div>
</div>
@endsection