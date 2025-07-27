<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link href="https://unpkg.com/cropperjs/dist/cropper.min.css" rel="stylesheet">
  <script src="https://unpkg.com/cropperjs/dist/cropper.min.js"></script>
  @yield('css')
  <title>coachtechフリマ</title>
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <div class="header__logo">
        <a href="/">
          <img class="header-logo" src="{{ asset('images/logo.svg') }}" alt="logo">
        </a>
      </div>

      @if (!request()->routeIs(['verification.notice'])
      && !(request()->routeIs('profile.edit') && session('profile_edit_first_time')))

      @auth
      <nav class="header-nav">
        <div class="header-nav__search">
          <form class="search-form" action="/" method="get">
            <input class="search-form__input" type="text" name="keyword" value="{{ request('keyword') }}"
              placeholder="何をお探しですか？">
            <button class="search-form__button" type="submit">検索</button>
          </form>
        </div>
        <div class="header-nav__links">
          <form class="header-nav__item header-nav__item--logout" action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="header-nav__button" type="submit">ログアウト</button>
          </form>
          <a class="header-nav__item header-nav__item--mypage" href="{{ route('profile.index') }}">マイページ</a>
          <a class="header-nav__item header-nav__item--sell" href="{{ route('items.create') }}">出品</a>
        </div>
      </nav>
      @endauth

      @guest
      @if (!request()->routeIs(['login', 'register']))
      <nav class="header-nav">
        <div class="header-nav__search">
          <form class="search-form" action="/" method="get">
            <input class="search-form__input" type="text" name="keyword" value="{{ request('keyword') }}"
              placeholder="何をお探しですか？">
            <button class="search-form__button" type="submit">検索</button>
          </form>
        </div>
        <div class="header-nav__links">
          <a class="header-nav__item header-nav__item--login" href="{{ route('login') }}">ログイン</a>
          <a class="header-nav__item header-nav__item--mypage" href="{{ route('login') }}">マイページ</a>
          <a class="header-nav__item header-nav__item--sell" href="{{ route('login') }}">出品</a>
        </div>
      </nav>
      @endif
      @endguest
      @endif
    </div>
  </header>

  @if (!request()->routeIs([
  'purchase.success',
  'purchase.cancel',
  'purchase.invalid',
  'register',
  ]))
  <div class="flash-container">
    @if (session('success') || session('error'))
    <div class="flash-message
      {{ session('success') ? 'flash-message--success' : 'flash-message--error' }}
      is-visible">
      {{ session('success') ?? session('error') }}
    </div>
    @endif
  </div>
  @endif

  <main class="main">
    @yield('content')
  </main>
  @yield('js')

  <script>
    window.onpageshow = function(event) {
      if (event.persisted) {
        window.location.reload();
      }
    };

    // フラッシュメッセージのフェードアウト
    document.addEventListener('DOMContentLoaded', () => {
      const flash = document.querySelector('.flash-message');
      const mainContent = document.querySelector('.main');

      function adjustMargin() {
        if (!mainContent) return;

        // 画面幅でヘッダー高さを切り替え
        const headerHeight = window.innerWidth <= 850 ? 65 : 82;
        const flashHeight = (flash && flash.classList.contains('is-visible')) ? flash.offsetHeight : 0;

        mainContent.style.marginTop = (headerHeight + flashHeight) + 'px';
      }

      adjustMargin();

      setTimeout(() => {
        mainContent.classList.add('transition-enabled');
      }, 100);

      // リサイズ時にも再計算
      window.addEventListener('resize', adjustMargin);

      if (flash && flash.classList.contains('is-visible')) {
        setTimeout(() => {
          flash.classList.remove('is-visible');
          flash.classList.add('is-hidden');
          adjustMargin();
          setTimeout(() => flash.remove(), 500);
        }, 3000);
      }
    });
  </script>
</body>

</html>