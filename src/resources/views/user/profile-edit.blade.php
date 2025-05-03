@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}">
@endsection

@section('content')
<div class="profile-form">
  <div class="profile-form__container">
    <h1 class="profile-form__heading content__heading">
      プロフィール設定
    </h1>

    <div class="profile-form__content">
      <form class="profile-form__form" action="{{ route('profile.update') }}" method="post"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- プロフィール画像 -->
        <div class="profile-form__image-area">
          <div class="profile-form__image-wrapper">
            @if ($user->profile_image)
            <img class="profile-form__image" src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像">
            @else
            <img class="profile-form__image--default" src="{{ asset('images/default-profile.svg') }}" alt="デフォルト画像">
            @endif
          </div>
          <div class="profile-form__file-button">
            <label class="button--file-select" for="profile_image">画像を選択する</label>
            <input class="form__input profile-form__input" type="file" name="profile_image" id="profile_image"
              style="display: none;">
          </div>
          <x-error-message field="profile_image"
            class="error-message {{ $errors->has('profile_image') ? 'has-error' : 'no-error' }}" />
        </div>
    </div>

    <!-- ユーザーデータ -->
    <!-- ユーザー名 -->
    <div class="profile-form__input-area">
      <div class="profile-form__group">
        <label class="form__label profile-form__label" for="name">ユーザー名</label>
        <input class="form__input profile-form__input" type="text" name="name" id="name"
          value="{{ old('name', $user->name) }}">
        <x-error-message field="name" class="error-message {{ $errors->has('name') ? 'has-error' : 'no-error' }}" />
      </div>

      <!-- 郵便番号 -->
      <div class="profile-form__group">
        <label class="form__label profile-form__label" for="postal_code">郵便番号</label>
        <input class="form__input profile-form__input" type="text" name="postal_code" id="postal_code"
          value="{{ old('postal_code', $user->postal_code) }}">
        <x-error-message field="postal_code"
          class="error-message {{ $errors->has('postal_code') ? 'has-error' : 'no-error' }}" />
      </div>

      <!-- 住所 -->
      <div class="profile-form__group">
        <label class="form__label profile-form__label" for="address">住所</label>
        <input class="form__input profile-form__input" type="text" name="address" id="address"
          value="{{ old('address', $user->address) }}">
        <x-error-message field="address"
          class="error-message {{ $errors->has('address') ? 'has-error' : 'no-error' }}" />
      </div>

      <!-- 建物名 -->
      <div class="profile-form__group">
        <label class="form__label profile-form__label" for="building">建物名</label>
        <input class="form__input profile-form__input" type="text" name="building" id="building"
          value="{{ old('building', $user->building) }}">
        <x-error-message field="building"
          class="error-message {{ $errors->has('building') ? 'has-error' : 'no-error' }}" />
      </div>
    </div>

    <!-- 送信ボタン -->
    <div class="profile-form__button">
      <button class="profile-form__button-submit" type="submit">更新する</button>
    </div>
    </form>
  </div>
</div>
@endsection