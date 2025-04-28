@props(['field' => null, 'messageOverride' => null, 'excludeMessage' => null])

@if ($field)
@php
$errorsForField = $errors->get($field);
$targetMessage = $errorsForField[0] ?? null;
$showMessage = false;

if ($targetMessage) {
if ($messageOverride !== null && $targetMessage === $messageOverride) {
$showMessage = true;
} elseif ($excludeMessage !== null && $targetMessage !== $excludeMessage) {
$showMessage = true;
} elseif ($messageOverride === null && $excludeMessage === null) {
$showMessage = true;
}
}

$baseClass = $attributes->get('class');
$className = $baseClass . ' ' . ($showMessage ? 'has-error' : 'no-error');
@endphp

<p class="{{ $className }}">
  {!! $showMessage ? $targetMessage : '&nbsp;' !!}
</p>
@endif