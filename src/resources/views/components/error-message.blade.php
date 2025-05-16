@props([
'field' => null,
'fields' => [],
'messageOverride' => null,
'excludeMessage' => null,
])

@php
// 単体指定ならフィールド配列に追加
if ($field) {
$fields[] = $field;
}

$targetMessage = null;
$showMessage = false;

foreach ((array) $fields as $f) {
$messages = $errors->get($f);
if (!empty($messages)) {
$targetMessage = $messages[0];
if ($messageOverride !== null && $targetMessage === $messageOverride) {
$showMessage = true;
} elseif ($excludeMessage !== null && $targetMessage !== $excludeMessage) {
$showMessage = true;
} elseif ($messageOverride === null && $excludeMessage === null) {
$showMessage = true;
}
break; // 最初に見つけたエラーだけ表示
}
}

$className = $attributes->get('class') . ' ' . ($showMessage ? 'has-error' : 'no-error');
@endphp

<p class="{{ $className }}">
  {!! $showMessage ? $targetMessage : '&nbsp;' !!}
</p>