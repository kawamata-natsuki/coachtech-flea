{{ $order->item->name }} の取引が完了しました。

レビューをお願いします。

取引画面はこちら：
{{ route('chat.index', ['order' => $order->id, 'from_email' => 1]) }}

このメールはシステムから自動送信されています。返信は不要です。