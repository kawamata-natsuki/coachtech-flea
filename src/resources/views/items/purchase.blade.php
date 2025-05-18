@extends('layouts.app')


@section('css')
<link rel="stylesheet" href="{{ asset('css/items/purchase.css') }}">
@endsection

@section('content')
<div class="item-purchase-page">
  <h1 class="sr-only">購入内容のご確認</h1>
  <div class="item-purchase-page__container">
    <div class="item-purchase-page__wrapper">

      <!-- 左 -->
      <div class="item-purchase-page__input-area">
        <!-- 商品情報 -->
        <div class="item-purchase-page__item-info">
          <div class="item-purchase-page__item-image">
            <img class="item-card__img" src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}">
          </div>
          <div class="item-purchase-page__item-summary">
            <p class="item-purchase-page__item-name">{{ $item->name }}</p>
            <p class="item-purchase-page__item-price">
              <span class="item-purchase-page__price-unit">¥ </span>{{ number_format($item->price) }}
            </p>
          </div>
        </div>

        <!-- 支払方法選択 -->
        <div class="item-purchase-page__payment-form">
          <p class="item-purchase-page__section-title">支払い方法</p>
          <div class="item-purchase-page__select-wrapper">
            <select class="item-purchase-page__select" name="payment_method" id="payment_method"
              onchange="updatePaymentMethod()">
              <option class="placeholder-option" value="" disabled selected hidden>選択してください</option>
              @foreach ($paymentMethods as $code => $label)
              <option value="{{ $code }}" {{ (old('payment_method') ?? $selectedPaymentMethod)===$code ? 'selected' : ''
                }}>
                {{ $label }}
              </option>
              @endforeach
            </select>
          </div>
          <x-error-message class="error-message error-under-select" field="payment_method" />
        </div>

        <!-- 住所 -->
        <div class="item-purchase-page__address">
          <div class="item-purchase-page__address-header">
            <p class="item-purchase-page__section-title">配送先</p>
            <a class="item-purchase-page__link"
              href="{{ route('address.edit', ['item' => $item->id, 'payment_method' => old('payment_method') ?? $selectedPaymentMethod]) }}">
              変更する
            </a>
          </div>

          <div class="item-purchase-page__address-body">
            <p><span class="item-purchase-page__postal-mark">〒 </span>{{ $user->postal_code }}</p>
            <p class="item-purchase-page__address-text">{{ $user->address }}</p>
            @if ($user->building)
            <p class="item-purchase-page__address-text">{{ $user->building }}</p>
            @endif
          </div>
          <x-error-message :fields="['postal_code', 'address']" class="error-message" />
        </div>
      </div>

      <!-- 右 -->
      <div class="item-purchase-page__purchase-summary">
        <div class="item-purchase-page__confirm">
          <div class="item-purchase-page__block">
            <p class="item-purchase-page__confirm-label">
              商品代金
            </p>
            <p class="item-purchase-page__confirm-value">
              ¥{{ number_format($item->price) }}
            </p>
          </div>
          <div class="item-purchase-page__block">
            <p class="item-purchase-page__confirm-label">支払い方法</p>
            <p class="item-purchase-page__confirm-value js-selected-method"></p>
          </div>
        </div>

        <!-- 購入ボタン -->
        <form method="POST" action="{{ route('purchase.store', $item->id) }}">
          @csrf
          <input type="hidden" name="payment_method" id="hidden_payment_method"
            value="{{ old('payment_method') ?? $selectedPaymentMethod }}">
          <input type="hidden" name="postal_code" value="{{ $user->postal_code }}">
          <input type="hidden" name="address" value="{{ $user->address }}">
          @if ($user->building)
          <input type="hidden" name="building" value="{{ $user->building }}">
          @endif
          <div class="item-purchase-page__button">
            <button type="submit" class="button--solid-red item-purchase-page__button-submit">購入する</button>
          </div>
        </form>
        <x-error-message class="error-message error-under-button" field="item_price" />
      </div>
    </div>
  </div>
</div>
@endsection

<!-- JavaSacript -->
@section('js')
<script>
  function updatePaymentMethod() {
    const select = document.getElementById('payment_method');
    const display = document.querySelector('.js-selected-method');
    const hidden = document.getElementById('hidden_payment_method');

    // 表示用
    const selectedIndex = select.selectedIndex;
    if (selectedIndex >= 0 && select.options[selectedIndex]) {
      const selectedText = select.options[selectedIndex].text;
      display.textContent = selectedText;
    }

    // 送信用
    hidden.value = select.value;
  }

  window.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('payment_method');
    const display = document.querySelector('.js-selected-method');
    const hidden = document.getElementById('hidden_payment_method');

    const hiddenValue = hidden.value; // ← hiddenの値を見る！

    if (hiddenValue) {
      for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].value === hiddenValue) {
          select.options[i].selected = true; // セレクトボックスを選択状態にする
          display.textContent = select.options[i].text; // 右側に支払方法名を表示する
          break;
        }
      }
    }
  });
</script>
@endsection