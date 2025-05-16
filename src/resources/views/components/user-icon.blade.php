@props([
'user',
'imageClass' => 'user-icon',
'defaultClass' => '',
'nameClass' => '',
'wrapperClass' => '',
])

@php
$isDefault = empty($user?->profile_image);
@endphp

<div class="{{ $wrapperClass }}">
  <img class="{{ $isDefault ? " $imageClass $defaultClass" : $imageClass }}"
    src="{{ $isDefault ? asset('images/icons/default-profile.svg') : asset('storage/' . $user->profile_image) }}"
    alt="プロフィール画像">

  <span class="{{ $nameClass }}">{{ $user?->name ?? '匿名ユーザー' }}</span>
</div>