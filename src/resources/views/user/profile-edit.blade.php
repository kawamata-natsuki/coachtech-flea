@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}">
<!-- Cropper.js CSS -->
<link href="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.css" rel="stylesheet">
<style>
  #cropper-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 999;
    padding: 20px;
    box-sizing: border-box;
  }

  #cropper-modal>div {
    background: white;
    padding: 16px;
    border-radius: 10px;
    max-width: 100%;
    max-height: 100%;
    overflow: auto;
    box-sizing: border-box;
  }

  #cropper-wrapper {
    width: 400px;
    height: 400px;
  }

  .cropper-mask {
    position: absolute;
    top: 0;
    left: 0;
    width: 400px;
    height: 400px;
  }

  .cropper-mask::after {
    width: 280px;
    height: 280px;
    top: 60px;
    left: 60px;
    border-radius: 50%
  }

  #cropper-image {
    max-width: 100%;
    display: block;
  }

  .cropper-crop-box,
  .cropper-view-box {
    border-radius: 50% !important;
  }

  .cropper-modal__buttons {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 20px;
  }

  #crop-button,
  #close-cropper {
    padding: 10px 24px;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s;
  }

  #crop-button {
    background-color: #007BFF;
    color: white;
  }

  #crop-button:hover {
    background-color: #0056b3;
  }

  #close-cropper {
    background-color: #e0e0e0;
    color: #333;
  }

  #close-cropper:hover {
    background-color: #c0c0c0;
  }
</style>
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
            <img id="preview-image" class="profile-form__image profile-form__image--custom"
              src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('images/default-profile.svg') }}"
              alt="プロフィール画像">
          </div>
          <div class="profile-form__file-button">
            <label class="button--file-select" for="select-image">画像を選択する</label>
            <input type="file" id="select-image" accept="image/*" style="display: none;">
            <input type="hidden" name="cropped_image" id="cropped-image-data">
          </div>
          <x-error-message field="profile_image"
            class="error-message {{ $errors->has('profile_image') ? 'has-error' : 'no-error' }}" />
        </div>

        <!-- Cropperモーダル -->
        <div id="cropper-modal">
          <div class="cropper-modal__content">
            <div id="cropper-wrapper">
              <img id="cropper-image">
              <div class="cropper-mask"></div>
            </div>

            <!-- ボタン並べる -->
            <div class="cropper-modal__buttons">
              <button type="button" id="crop-button">切り抜いて決定</button>
              <button type="button" id="close-cropper" aria-label="モーダルを閉じる">キャンセル</button>
            </div>
          </div>
        </div>

        <!-- ユーザーデータ -->
        <div class="profile-form__input-area">
          <!-- ユーザー名 -->
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
</div>
@endsection

@section('js')
<script src="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.js"></script>
<script>
  let cropper;

  document.getElementById('select-image').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      const image = document.getElementById('cropper-image');
      image.src = e.target.result;
      document.getElementById('cropper-modal').style.display = 'flex';

      if (cropper) cropper.destroy();
      cropper = new Cropper(image, {
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

      // 🔽 ここで value をクリアするのがポイント！
      document.getElementById('select-image').value = '';
    };
    reader.readAsDataURL(file);
  });

  document.getElementById('crop-button').addEventListener('click', function () {
    const cropBox = cropper.getCropBoxData();

    const canvas = cropper.getCroppedCanvas({
      width: 280,
      height: 280,
    });

    const preview = document.getElementById('preview-image');
    const croppedData = canvas.toDataURL('image/jpeg');

    preview.src = croppedData;
    document.getElementById('cropped-image-data').value = croppedData;
    document.getElementById('cropper-modal').style.display = 'none';

    cropper.destroy();
  });

  document.getElementById('close-cropper').addEventListener('click', function () {
    document.getElementById('cropper-modal').style.display = 'none';
    if (cropper) {
      cropper.destroy();
    }
  });
</script>
@endsection