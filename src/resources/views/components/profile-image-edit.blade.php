@props(['user'])

@php
$isDefault = empty($user?->profile_image);
$imageSrc = $isDefault
? asset('images/icons/default-profile.svg')
: asset('storage/' . $user->profile_image);
$imageClass = $isDefault
? 'profile-edit-page__image profile-edit-page__image--default'
: 'profile-edit-page__image profile-edit-page__image--custom';
@endphp

<div class="profile-edit-page__image-wrapper">
  <img src="{{ $imageSrc }}" alt="プロフィール画像" class="js-preview-image {{ $imageClass }}">
</div>
<div class="profile-edit-page__file-button">
  <button type="button" class="js-trigger-file">画像を選択</button>
  <input type="file" class="js-image-input" hidden>
  <input type="hidden" name="cropped_image" class="js-cropped-data">
</div>
<x-error-message field="profile_image"
  class="error-message {{ $errors->has('profile_image') ? 'has-error' : 'no-error' }}" />