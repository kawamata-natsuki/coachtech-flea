@php
use App\Constants\CategoryConstants;
use App\Constants\ConditionConstants;
@endphp

@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/create.css') }}">
@endsection

@section('content')
<div class="create-page">
  <div class="create-page__container">
    <h1 class="create-page__heading content__heading">商品の出品</h1>

    <div class="create-page__content">
      <form class="create-form__form" action="{{ route('items.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <!-- 商品画像 -->
        <div class="create-page__field">
          <label class="create-page__label">商品画像</label>
          <div class="create-page__upload-area" id="drop-area">
            <span class="upload-instruction button--outline-red">画像を選択する</span>
            <img id="preview" class="image-preview" style="display: none;" alt="プレビュー">
            <button type="button" id="remove-image" class="remove-image" style="display: none;">✕</button>
          </div>
          <input type="file" name="item_image" accept="image/*" id="file-input" class="create-page__file-input">
          <x-error-message class="error-message" field="item_image" />
        </div>

        <!-- 商品詳細 -->
        <div class="create-page__section">
          <h2 class="create-page__section-title">商品の詳細</h2>

          <!-- カテゴリー -->
          <div class="create-page__field">
            <label class="create-page__label">カテゴリー</label>
            <div class="create-page__tags">
              @foreach (CategoryConstants::LABELS as $code => $label)
              <input class="create-page__tag-checkbox" type="checkbox" name="category_codes[]" value="{{ $code }}"
                id="category_{{ $code }}" {{ is_array(old('category_codes')) && in_array($code, old('category_codes',
                [])) ? 'checked' : '' }}>
              <label for="category_{{ $code }}" class="create-page__tag">{{ $label }}</label>
              @endforeach
            </div>
            <x-error-message class="error-message" field="category_codes" />
          </div>

          <!-- 状態 -->
          <div class="create-page__field">
            <label class="create-page__label" for="condition_code">商品の状態</label>
            <div class="create-page__select-wrapper">
              <select class="create-page__select" name="condition_code" id="condition_code">
                <option value="">選択してください</option>
                @foreach (ConditionConstants::LABELS as $code => $label)
                <option value="{{ $code }}" {{ old('condition_code')===$code ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
              <x-error-message class="error-message" field="condition_code" />
            </div>
          </div>
        </div>

        <!-- 商品名と説明 -->
        <div class="create-page__section">
          <h2 class="create-page__section-title">商品名と説明</h2>

          <div class="create-page__field">
            <label class="create-page__label" for="name">商品名</label>
            <input class="create-page__input" name="name" type="text" value="{{ old('name') }}">
            <x-error-message class="error-message" field="name" />
          </div>

          <div class="create-page__field">
            <label class="create-page__label" for="brand">ブランド名</label>
            <input class="create-page__input" name="brand" type="text" value="{{ old('brand') }}">
            <x-error-message class="error-message" field="brand" />
          </div>

          <div class="create-page__field">
            <label class="create-page__label" for="description">商品の説明</label>
            <textarea class="create-page__textarea" name="description">{{ old('description') }}</textarea>
            <x-error-message class="error-message" field="description" />
          </div>

          <div class="create-page__field">
            <label class="create-page__label" for="price">販売価格</label>
            <div class="create-page__input-wrapper">
              <span class="create-page__input-prefix">￥</span>
              <input class="create-page__input create-page__input--price" name="price" type="number"
                value="{{ old('price') }}">
            </div>
            <x-error-message class="error-message" field="price" />
          </div>
        </div>

        <!-- ボタン -->
        <div class="item-create__button-wrapper">
          <button class="button--solid-red create-button" type="submit">出品する</button>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // 画像アップロード処理
    const dropArea = document.getElementById('drop-area');
    const input = document.getElementById('file-input');
    const preview = document.getElementById('preview');
    const instruction = dropArea.querySelector('.upload-instruction');
    const removeButton = document.getElementById('remove-image');

    function showPreview(file) {
      const url = URL.createObjectURL(file);
      preview.src = url;
      preview.style.display = 'block';
      instruction.style.display = 'none';
      removeButton.style.display = 'block';
    }

    dropArea.addEventListener('click', () => input.click());

    input.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file && file.type.startsWith('image/')) {
        showPreview(file);
      } else {
        alert('画像ファイルを選択してください');
      }
    });

    ['dragenter', 'dragover'].forEach(evt => {
      dropArea.addEventListener(evt, e => {
        e.preventDefault();
        dropArea.classList.add('dragover');
      });
    });

    ['dragleave', 'drop'].forEach(evt => {
      dropArea.addEventListener(evt, e => {
        e.preventDefault();
        dropArea.classList.remove('dragover');
      });
    });

    dropArea.addEventListener('drop', (e) => {
      const file = e.dataTransfer.files[0];
      if (file && file.type.startsWith('image/')) {
        input.files = e.dataTransfer.files;
        showPreview(file);
      } else {
        alert('画像ファイルをドロップしてください');
      }
    });

    // ✕ボタンで画像をリセット
    removeButton.addEventListener('click', (e) => {
      e.stopPropagation(); // dropArea の click が発火しないようにする
      preview.src = '';
      preview.style.display = 'none';
      removeButton.style.display = 'none';
      input.value = '';
      instruction.style.display = 'block';
    });

    // **二重送信防止**
    const form = document.querySelector('.create-form__form');
    if (form) {
      form.addEventListener('submit', (e) => {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
      });
    }
  });
</script>
@endsection