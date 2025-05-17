@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/profile-address.css') }}">
@endsection

@section('content')
<div class="profile-address-page">
  <div class="profile-address-page__container">
    <h1 class="profile-address-page__heading content__heading">
      住所の変更
    </h1>

    <div class="profile-address-page__content">
      <form class="profile-address-page__form" action="{{ route('address.update' ,['item' => $item->id]) }}"
        method="post" enctype="multipart/form-data">
        <input type="hidden" name="payment_method" value="{{ old('payment_method') ?? request('payment_method') }}">
        @csrf
        @method('PUT')

        <!-- 郵便番号 -->
        <div class="profile-address-page__group">
          <label class="form__label profile-address-page__label" for="postal_code">郵便番号</label>
          <input class="form__input profile-address-page__input" type="text" name="postal_code" id="postal_code"
            value="{{ old('postal_code', $user->postal_code) }}">
          <x-error-message field="postal_code"
            class="error-message {{ $errors->has('postal_code') ? 'has-error' : 'no-error' }}" />
        </div>

        <!-- 住所 -->
        <div class="profile-address-page__group">
          <label class="form__label profile-address-page__label" for="address">住所</label>
          <input class="form__input profile-address-page__input" type="text" name="address" id="address"
            value="{{ old('address', $user->address) }}">
          <x-error-message field="address"
            class="error-message {{ $errors->has('address') ? 'has-error' : 'no-error' }}" />
        </div>

        <!-- 建物名 -->
        <div class="profile-address-page__group">
          <label class="form__label profile-address-page__label" for="building">建物名</label>
          <input class="form__input profile-address-page__input" type="text" name="building" id="building"
            value="{{ old('building', $user->building) }}">
          <x-error-message field="building"
            class="error-message {{ $errors->has('building') ? 'has-error' : 'no-error' }}" />
        </div>

        <!-- 送信ボタン -->
        <div class="profile-address-page__button">
          <button type="submit" class="button--solid-red profile-address-page__button-submit">変更を保存</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection