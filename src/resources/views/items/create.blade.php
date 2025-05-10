@php
use App\Constants\CategoryConstants;
use App\Constants\ConditionConstants;
@endphp


@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endsection

@section('content')
<div class="create-page">
  <div class="create-page__container">
    <h1 class="create-page__heading content__heading">
      商品の出品
    </h1>

    <div class="create-page__content">
      <form class="create-form__form" action="{{ route('items.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <!-- 商品画像のアップロード -->
        <!-- Figmaではドラッグ&ドロップできそうだけど、JS必須 -->
        <div class="create-page__field">
          <label class="create-page__label">
            商品画像
          </label>
          <div class="create-page__upload-area">
            <label class="create-page__upload-button">
              画像を選択する
              <input type="file" name="item_image" accept="image/*" class="create-page__file-input">
            </label>
          </div>
          <x-error-message class="error-message" field="item_image" />
        </div>

        <div class="create-page__section">
          <h2 class="create-page__section-title">
            商品の詳細
          </h2>

          <!-- カテゴリを表示、クリックで選択できる -->
          <div class="create-page__field">
            <label class="create-page__label">カテゴリー</label>
            <div class="create-page__tags">
              @foreach (CategoryConstants::LABELS as $code => $label)
              <input type="checkbox" name="category_codes[]" value="{{ $code }}" id="category_{{ $code }}"
                class="create-page__tag-checkbox">
              <label for="category_{{ $code }}" class="create-page__tag">{{ $label }}</label>
              @endforeach
            </div>
            <x-error-message class="error-message" field="category_codes" />
          </div>

          <!-- 商品の状態をプルダウンで選択 -->
          <div class="create-page__field">
            <label class="create-page__label">商品の状態</label>

            <div class="create-page__select-wrapper">
              <select name="condition_code" class="create-page__select">
                <option value="">選択してください</option>
                @foreach (ConditionConstants::LABELS as $code => $label)
                <option value="{{ $code }}">{{ $label }}</option>
                @endforeach
              </select>
              <x-error-message class="error-message" field="condition_code" />
            </div>
          </div>
        </div>

        <div class="create-page__section">
          <h2 class="create-page__section-title">
            商品名と説明
          </h2>

          <!-- 商品名 -->
          <div class="create-page__field">
            <label class="create-page__label">商品名</label>
            <input name="name" type="text" class="create-page__input">
            <x-error-message class="error-message" field="name" />
          </div>

          <!-- ブランド名 -->
          <div class="create-page__field">
            <label class="create-page__label">ブランド名</label>
            <input name="brand" type="text" class="create-page__input">
            <x-error-message class="error-message" field="brand" />
          </div>

          <!-- 商品の説明 -->
          <div class="create-page__field">
            <label class="create-page__label">商品の説明</label>
            <textarea class="create-page__textarea" name="description"></textarea>
            <x-error-message class="error-message" field="price" />
          </div>

          <!-- 販売価格 -->
          <div class="create-page__field">
            <label class="create-page__label">販売価格</label>

            <div class="create-page__input-wrapper">
              <span class="create-page__input-prefix">￥</span>
              <input class="create-page__input  create-page__input--price" name="price" type="number">
            </div>
            <x-error-message class="error-message" field="price" />
          </div>
        </div>

        <!-- 出品するボタン -->
        <div class="item-create__button-wrapper">
          <button class="create-button" type="submit">出品する</button>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection