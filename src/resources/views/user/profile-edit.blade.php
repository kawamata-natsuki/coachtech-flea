@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/profile-edit.css') }}">
<!-- Cropper.js CSS -->
<link href="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/components/cropper-modal.css') }}">
@endsection

@section('content')
<div class="profile-edit-page">
  <div class="profile-edit-page__container">
    <h1 class="profile-edit-page__heading content__heading">
      プロフィール設定
    </h1>

    <div class="profile-edit-page__content">
      <form class="profile-edit-page__form" action="{{ route('profile.update') }}" method="post"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- プロフィール画像 -->
        <div class="profile-edit-page__image-area">
          <x-profile-image-edit :user="$user" />
        </div>

        <!-- Cropperモーダル -->
        <x-cropper-modal />

        <!-- ユーザーデータ -->
        <div class="profile-edit-page__form-section">
          <!-- ユーザー名 -->
          <div class="profile-edit-page__form-group">
            <label class="form__label profile-edit-page__label" for="name">ユーザー名</label>
            <input class="form__input profile-edit-page__input" type="text" name="name" id="name"
              value="{{ old('name', $user->name) }}">
            <x-error-message field="name" class="error-message {{ $errors->has('name') ? 'has-error' : 'no-error' }}" />
          </div>

          <!-- 郵便番号 -->
          <div class="profile-edit-page__form-group">
            <label class="form__label profile-edit-page__label" for="postal_code">郵便番号</label>
            <input class="form__input profile-edit-page__input" type="text" name="postal_code" id="postal_code"
              value="{{ old('postal_code', $user->postal_code) }}">
            <x-error-message field="postal_code"
              class="error-message {{ $errors->has('postal_code') ? 'has-error' : 'no-error' }}" />
          </div>

          <!-- 住所 -->
          <div class="profile-edit-page__form-group">
            <label class="form__label profile-edit-page__label" for="address">住所</label>
            <input class="form__input profile-edit-page__input" type="text" name="address" id="address"
              value="{{ old('address', $user->address) }}">
            <x-error-message field="address"
              class="error-message {{ $errors->has('address') ? 'has-error' : 'no-error' }}" />
          </div>

          <!-- 建物名 -->
          <div class="profile-edit-page__form-group">
            <label class="form__label profile-edit-page__label" for="building">建物名</label>
            <input class="form__input profile-edit-page__input" type="text" name="building" id="building"
              value="{{ old('building', $user->building) }}">
            <x-error-message field="building"
              class="error-message {{ $errors->has('building') ? 'has-error' : 'no-error' }}" />
          </div>
        </div>

        <!-- 送信ボタン -->
        <div class="profile-edit-page__button">
          <button type="submit" class="button--solid-red profile-edit-page__button-submit">
            更新する </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const imageInput = document.querySelector('.js-image-input');
    const previewImage = document.querySelector('.js-preview-image');
    const croppedDataInput = document.querySelector('.js-cropped-data');
    const cropperModal = document.querySelector('.js-cropper-modal');
    const cropperImage = document.querySelector('.js-cropper-image');
    const cropButton = document.querySelector('.js-crop-button');
    const closeButton = document.querySelector('.js-crop-close');
    const triggerFileButton = document.querySelector('.js-trigger-file');

    let cropper;

    // ✅ 画像選択ボタンのクリックで input を起動
    if (triggerFileButton && imageInput) {
      triggerFileButton.addEventListener('click', () => {
        imageInput.click();
      });
    }

    if (!imageInput || !previewImage || !cropperModal || !cropperImage) return;

    imageInput.addEventListener('change', e => {
      const file = e.target.files[0];
      if (!file) return;

      const reader = new FileReader();

      reader.onload = () => {
        cropperImage.src = reader.result;

        cropperImage.onload = () => {
          cropperModal.style.display = 'flex';
          document.body.style.overflow = 'hidden';

          if (cropper) cropper.destroy();
          cropper = new Cropper(cropperImage, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 1,
            dragMode: 'move',
            cropBoxMovable: false,
            cropBoxResizable: false,
            background: false,
            ready() {
              const containerData = cropper.getContainerData();
              const boxSize = 280;
              cropper.setCropBoxData({
                width: boxSize,
                height: boxSize,
                left: (containerData.width - boxSize) / 2,
                top: (containerData.height - boxSize) / 2
              });
              cropper.zoom(-0.2);
            }
          });
        };
      };

      reader.readAsDataURL(file);
    });

    cropButton?.addEventListener('click', () => {
      const canvas = cropper.getCroppedCanvas({
        width: 280,
        height: 280
      });
      const croppedData = canvas.toDataURL('image/jpeg');
      previewImage.src = croppedData;
      previewImage.classList.remove('profile-edit-page__image--default');
      previewImage.classList.add('profile-edit-page__image--custom');
      croppedDataInput.value = croppedData;
      cropperModal.style.display = 'none';
      document.body.style.overflow = '';
      cropper.destroy();
    });

    closeButton?.addEventListener('click', () => {
      cropperModal.style.display = 'none';
      document.body.style.overflow = '';
      cropper?.destroy();
    });
  });
</script>
@endsection