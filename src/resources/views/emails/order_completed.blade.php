<div style="font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.6; max-width: 500px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px;">

  <h2 style="color: #333; text-align: center; margin-bottom: 20px; word-break: break-word; overflow-wrap: break-word;">
    {{ $order->item->name }} の取引が完了しました。
  </h2>

  <p style="color: #333; margin-bottom: 20px; text-align: center;">
    レビューをお願いします。
  </p>

  <div style="text-align: center; margin: 20px 0;">
    <a href="{{ route('chat.index', ['order' => $order->id, 'from_email' => 1]) }}"
      style="display: inline-block; padding: 12px 24px; background-color: #FF5555; color: #fff; text-decoration: none; font-weight: bold; border-radius: 5px; font-size: 14px;">
      取引画面を開く
    </a>
  </div>

  <p style="text-align: center; font-size: 12px; color: #777;">
    このメールはシステムから自動送信されています。返信は不要です。
  </p>

</div>