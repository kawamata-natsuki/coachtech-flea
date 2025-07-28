@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat/modal.css') }}">
<link rel="stylesheet" href="{{ asset('css/chat/index.css') }}">
@endsection

@section('content')
<div class="chat-page">
  <aside class="chat-page-sidebar">
    <div class="chat-page-sidebar__title">その他の取引</div>
    <ul class="chat-page-sidebar__list">
      @foreach ($tradingItems as $tradingItem)
      <li class="chat-page-sidebar__item {{ $order->id === $tradingItem->id ? 'active' : '' }}">
        <a href="{{ route('chat.index', $tradingItem->id) }}" class="chat-page-sidebar__item--link">
          {{ $tradingItem->item->name }}
          @if($tradingItem->unread_count > 0)
          <span class="badge-unread">
            {{ $tradingItem->unread_count > 99 ? '99+' : $tradingItem->unread_count }}
          </span>
          @endif
        </a>
      </li>
      @endforeach
    </ul>
  </aside>

  <div class=" chat-page__container">

    <div class="chat-page__header">
      <h1 class="chat-page__heading content__heading">
        <x-user-icon :user="$chatPartner"
          wrapperClass="chat-page__user"
          imageClass="user-icon"
          defaultClass="user-icon--default"
          nameClass="user-name" />
        <p>さんとの取引画面</p>
      </h1>

      @auth
      @if (auth()->id() === $order->user_id && $order->order_status === \App\Constants\OrderStatus::PENDING)
      <form action="{{ route('order.complete', $order->id) }}" method="POST">
        @csrf
        @method('PUT')
        <button type="submit" class="transaction-completed">
          取引を完了する
        </button>
      </form>
      @endif
      @endauth
    </div>

    <!-- 商品情報 -->
    <div class="chat-page__item">
      <div class="chat-page__item-image">
        <img class="item-card__img" src="{{ asset('storage/' . $item->item_image) }}" alt="{{ $item->name }}">
      </div>
      <div class="chat-page__item-info">
        <p class="chat-page__item-name">
          {{ $item->name }}
        </p>
        <p class="chat-page__item-price">
          <span class="chat-page__price-unit">¥ </span>
          {{ number_format($item->price) }}
        </p>
      </div>
      <div class="chat-page__line-bottom"></div>
    </div>

    <!-- チャット一覧 -->
    <div class="chat-page__messages">
      @if ($messages->isEmpty())
      <div class="chat-page__empty-message">
        まだメッセージはありません
      </div>
      @else
      <ul class="chat-page__message-list">
        @foreach ($messages as $message)
        <li class="chat-page__message-item {{ $message->user_id === Auth::id() 
          ? 'my-message' 
          : 'partner-message' }}"
          id="message-{{ $message->id }}">

          <!-- アイコン+ユーザー名 -->
          <div class="chat-page__message-user">
            <x-user-icon :user="$message->user"
              wrapperClass="chat-page__message-user"
              imageClass="chat-page__message-icon"
              defaultClass="chat-page__message-icon--default"
              nameClass="chat-page__message-name" />
          </div>

          <!-- アイコン+ユーザー名＋テキスト＋画像＋編集・削除 -->
          <div class="chat-page__message-body">
            <div class="chat-page__message-text-wrapper">
              <div class="chat-page__message-text js-message-text">
                {{ $message->message }}
              </div>
              <form action="{{ route('chat.update', $message->id) }}" method="POST"
                class="chat-page__message-edit-form js-message-edit-form" style="display:none;">
                @csrf
                @method('PUT')
                <textarea name="message" class="chat-page__message-input" rows="2">{{ $message->message }}</textarea>
                <div class="chat-page__edit-actions">
                  <button type="submit" class="chat-page__button-save">保存</button>
                  <button type="button" class="chat-page__button-cancel" onclick="cancelEdit(this)">キャンセル</button>
                </div>
              </form>
            </div>

            @if($message->chat_image)
            <div class="chat-page__message-image">
              <img src="{{ asset('storage/' . $message->chat_image) }}" alt="チャット画像" class="chat-message-img">
            </div>
            @endif

            <!-- 編集・削除ボタン -->
            @if ($message->user_id === Auth::id())
            <div class="chat-page__message-actions">
              <button type="button" class="chat-page__button-edit" onclick="enableEdit(this)">編集</button>
              <form action="{{ route('chat.destroy', $message->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="chat-page__button-delete"
                  onclick="return confirm('削除してもよろしいですか？')">削除</button>
              </form>
            </div>
            @endif
          </div>
        </li>
        @endforeach
      </ul>
      @endif
    </div>

    <!-- メッセージ送信フォーム -->
    <form action="{{ route('chat.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="chat-page__form">
      @csrf

      <!-- エラーメッセージ -->
      <div class="chat-form-wrapper">
        @if ($errors->any())
        <div class="error-message has-error">
          {{ $errors->first() }}
        </div>
        @endif

        <!-- 入力フォーム -->
        <div class="chat-page__form-body">
          <!-- 画像プレビュー -->
          <div id="image-preview" class="chat-form__preview" style="display:none;">
            <img id="preview-img" alt="プレビュー画像" style="max-width:100px; max-height:100px; border:1px solid #ccc; border-radius:4px;">
            <button type="button" class="remove-btn remove-preview-btn" onclick="removePreview()">×</button>
            <p id="preview-name" style="font-size:12px; color:#666; margin-top:4px;"></p>
          </div>

          <!-- 入力フォーム＋ボタン -->
          <div class="chat-form__input-row">
            <!-- メッセージ入力 -->
            <input
              type="text"
              name="message"
              id="chatMessage"
              class="chat-form__input"
              placeholder="取引メッセージを記入してください"
              value="{{ old('message') }}">

            <!-- 画像追加ボタン -->
            <label class="chat-form__image-label">
              <input type="file" name="chat_image" class="chat-form__image-input" hidden onchange="previewImage(this)">
              画像を追加
            </label>

            <!-- 送信ボタン -->
            <button type="submit" class="chat-form__send">
              <img src="{{ asset('images/icons/paper-plane.jpg') }}" class="chat-form__send-icon" alt="送信">
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- レビューモーダル -->
<div id="reviewModal" class="modal" style="display:none;">
  <div class="modal__overlay"></div>
  <div class="modal__content">
    <h2 class="modal__title">取引が完了しました。</h2>
    <p class="modal__text">今回の取引相手はどうでしたか？</p>
    <form action="{{ route('reviews.store', $order->id) }}" method="POST" class="modal__form">
      @csrf
      <!-- 星評価 -->
      <div class="modal__rating-wrapper">
        <div class="modal__rating">
          @for ($i = 5; $i >= 1; $i--)
          <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" required>
          <label for="star{{ $i }}">★</label>
          @endfor
        </div>
      </div>
      <div class="modal__button-wrapper">
        <button class="modal__button" type="submit">
          送信する
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('js')
<script>
  function enableEdit(button) {
    const wrapper = button.closest('.chat-page__message-body');
    const textDiv = wrapper.querySelector('.js-message-text');
    const editForm = wrapper.querySelector('.js-message-edit-form');
    const textarea = editForm.querySelector('.chat-page__message-input');

    // 表示時の高さを textarea に適用
    textarea.style.height = textDiv.offsetHeight + 'px';

    textDiv.style.display = 'none';
    editForm.style.display = 'block';

    const actions = wrapper.querySelector('.chat-page__message-actions');
    if (actions) actions.style.display = 'none';
  }

  function cancelEdit(button) {
    const wrapper = button.closest('.chat-page__message-body');
    const textDiv = wrapper.querySelector('.js-message-text');
    const editForm = wrapper.querySelector('.js-message-edit-form');

    textDiv.style.display = 'block';
    editForm.style.display = 'none';

    const actions = wrapper.querySelector('.chat-page__message-actions');
    if (actions) actions.style.display = 'flex';
  }

  function autoResize() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
  }

  document.querySelectorAll('.chat-page__message-input').forEach(textarea => {
    textarea.addEventListener('input', autoResize);
  });

  function removePreview() {
    const input = document.querySelector('.chat-form__image-input');
    input.value = '';
    document.getElementById('image-preview').style.display = 'none';
    document.getElementById('preview-img').src = '';
    document.getElementById('preview-name').textContent = '';
  }

  function previewImage(input) {
    const previewContainer = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const previewName = document.getElementById('preview-name');

    if (input.files && input.files[0]) {
      const file = input.files[0];
      const reader = new FileReader();

      reader.onload = function(e) {
        previewImg.src = e.target.result;
        previewContainer.style.display = 'block';
        previewName.textContent = file.name;
      };
      reader.readAsDataURL(file);
    } else {
      previewContainer.style.display = 'none';
      previewImg.src = '';
      previewName.textContent = '';
    }
  }

  const chatInput = document.getElementById('chatMessage');
  const STORAGE_KEY = 'chat_message_draft_{{ $order->id }}';

  if (chatInput) {
    document.addEventListener('DOMContentLoaded', () => {
      const savedMessage = localStorage.getItem(STORAGE_KEY);
      if (savedMessage && !chatInput.value) {
        chatInput.value = savedMessage;
      }
    });

    chatInput.addEventListener('input', () => {
      localStorage.setItem(STORAGE_KEY, chatInput.value);
    });

    document.querySelector('form.chat-page__form').addEventListener('submit', () => {
      localStorage.removeItem(STORAGE_KEY);
    });
  }
</script>

<!-- 購入者用（?review=1 のとき） -->
@if (request()->has('review'))
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.querySelector('#reviewModal');
    if (modal) {
      modal.style.display = 'block';
      modal.classList.add('is-active');
      history.replaceState({}, '', '?review=1');
    }
  });
</script>
@endif

<!-- 出品者用（COMPLETED_PENDING のとき） -->
@if (
auth()->id() === $order->item->user_id &&
$order->order_status === \App\Constants\OrderStatus::COMPLETED_PENDING &&
$order->reviews()->where('reviewer_id', $order->user_id)->exists()
)
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.querySelector('#reviewModal');
    if (modal) {
      modal.style.display = 'block';
      modal.classList.add('is-active');
    }
  });
</script>
@endif
@endsection