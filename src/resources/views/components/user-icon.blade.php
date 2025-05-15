@props(['user'])

@php
$isDefault = empty($user?->profile_image);
@endphp

<!-- ユーザーのプロフィール画像と名前を表示 -->
<img class="{{ $isDefault ? 'user-icon user-icon--default' : 'user-icon' }}"
  src="{{ $isDefault ? asset('images/icons/default-profile.svg') : asset('storage/' . $user->profile_image) }}"
  alt="プロフィール画像">
<span class="item-detail-page__comment-user">{{ $user?->name ?? '匿名ユーザー' }}</span>