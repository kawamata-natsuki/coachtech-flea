@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile-address.css') }}">
@endsection

@section('content')
<div class="profile-form">
  <div class="profile-form__container">
    <h1 class="profile-form__heading content__heading">
      住所の変更
    </h1>

    <div class="profile-form__content">
      <form class="profile-form__form" action="{{ route('address.update' ,['item' => $item->id]) }}" method="post"
        enctype="multipart/form-data">
        <input type="hidden" name="payment_method" value="{{ old('payment_method') ?? request('payment_method') }}">

        @csrf
        @method('PUT')

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


        <!-- 送信ボタン -->
        <div class="profile-form__button">
          <button class="profile-form__button-submit" type="submit">更新する</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection