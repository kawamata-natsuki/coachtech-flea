@props(['user'])

@php
$isDefault = empty($user?->profile_image);
$imageSrc = $isDefault
? asset('images/icons/default-profile.svg')
: asset('storage/' . $user->profile_image);
$imageClass = $isDefault
? 'profile-form__image profile-form__image--default'
: 'profile-form__image profile-form__image--custom';
@endphp

<div class="profile-form__image-wrapper">
  <img src="{{ $imageSrc }}" alt="プロフィール画像" class="js-preview-image {{ $imageClass }}">
</div>
<div class="profile-form__file-button">
  <label class="button--file-select">
    画像を選択する
    <input type="file" accept="image/*" hidden class="js-image-input">
  </label>
  <input type="hidden" name="cropped_image" class="js-cropped-data">
</div>
<x-error-message field="profile_image"
  class="error-message {{ $errors->has('profile_image') ? 'has-error' : 'no-error' }}" />