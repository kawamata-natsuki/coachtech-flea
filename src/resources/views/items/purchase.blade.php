@extends('layouts.app')


@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="item-purchase">
  <x-error-message class="error-message" field="item" />
  <div class="item-purchase__container">
    <div class="item-purchase__wrapper">

      <!-- 左 -->
      <div class="item-purchase__left">
        <!-- 商品情報 -->
        <div class="item-purchase__item-info">
          <div class="item-purchase__image">
            <img class="item-card__img" src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}">
          </div>
          <div class="item-purchase__text">
            <h1 class="item-purchase__name">{{ $item->name }}</h1>
            <p class="item-purchase__price">
              <span class="item-purchase__price-unit">¥ </span>{{ number_format($item->price) }}
            </p>
          </div>
        </div>


        <!-- 支払方法選択 -->
        <div class="item-purchase__payment-form">
          <p class="item-purchase__section-title">支払方法</p>
          <div class="item-purchase__select-wrapper">
            <select class="item-purchase__select" name="payment_method" id="payment_method"
              onchange="updatePaymentMethod()">
              <option class="placeholder-option" value="" disabled selected hidden>選択してください</option>
              @foreach ($paymentMethods as $code => $label)
              <option value="{{ $code }}" {{ (old('payment_method') ?? $selectedPaymentMethod)===$code ? 'selected' : ''
                }}>
                {{ $label }}
              </option>
              @endforeach
            </select>
            <x-error-message class="error-message" field="payment_method" />
          </div>
        </div>

        <!-- 住所 -->
        <div class="item-purchase__address">
          <div class="item-purchase__address-header">
            <p class="item-purchase__section-title">配送先</p>
            <a class="item-purchase__link"
              href="{{ route('address.edit', ['item' => $item->id, 'payment_method' => old('payment_method') ?? $selectedPaymentMethod]) }}">
              変更する
            </a>
          </div>

          <div class="item-purchase__address-body">
            <p><span class="item-purchase__postal-mark">〒 </span>{{ $user->postal_code }}</p>
            <p>{{ $user->address }}</p>
            @if ($user->building)
            <p>{{ $user->building }}</p>
            @endif
          </div>
        </div>
      </div>

      <!-- 右 -->
      <div class="item-purchase__right">
        <div class="item-purchase__confirm">
          <div class="item-purchase__block">
            <p class="item-purchase__confirm-price">
              商品代金
            </p>
            <p class="item-purchase__confirm-value">
              ¥{{ number_format($item->price) }}
            </p>
          </div>
          <div class="item-purchase__block">
            <p class="item-purchase__confirm-price">支払方法</p>
            <p id="selected-method" class="item-purchase__confirm-value"></p>
          </div>
        </div>

        <form method="POST" action="{{ route('purchase.store', $item->id) }}">
          @csrf
          <input type="hidden" name="payment_method" id="hidden_payment_method"
            value="{{ old('payment_method') ?? $selectedPaymentMethod }}">
          <input type="hidden" name="postal_code" value="{{ $user->postal_code }}">
          <input type="hidden" name="address" value="{{ $user->address }}">
          @if ($user->building)
          <input type="hidden" name="building" value="{{ $user->building }}">
          @endif
          <div class="item-purchase__button-wrapper">
            <button class="purchase-button" type="submit">購入する</button>
          </div>
          <!-- テスト用カード番号　 4242 4242 4242 4242　成功処理 -->
          <!-- テスト用カード番号　 4000 0000 0000 9995　失敗テスト -->
          <!-- 有効期限とCVCは適当な数字でOK -->
          <!-- READMEに記述しておく -->
        </form>
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
    const display = document.getElementById('selected-method');
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

  window.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('payment_method');
    const display = document.getElementById('selected-method');
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