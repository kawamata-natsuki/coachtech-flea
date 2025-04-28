@props(['class' => '', 'passwordMessage' => 'パスワードと一致しません'])

@php
$confirmError = $errors->first('password_confirmation');
$passwordErrors = $errors->get('password') ?? [];
$hasMismatch = in_array($passwordMessage, $passwordErrors);
$message = $confirmError ?: ($hasMismatch ? $passwordMessage : null);

// ✅ クラス追加ロジック
$finalClass = trim($class . ' ' . ($message ? 'has-error' : 'no-error'));
@endphp

<p class="{{ $finalClass }}">
  {{ $message ?? "\u{00A0}" }}
</p>