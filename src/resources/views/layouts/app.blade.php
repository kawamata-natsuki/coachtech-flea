<!DOCTYPE html>
<!-- ヘッダーロゴにトップページのリンクつけてます -->
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  @yield('css')
  <title>coachtech-flea</title>
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <div class="header__logo">
        <a href="/">
          <img class="header-logo" src="{{ asset('images/logo.svg') }}" alt="logo">
        </a>
      </div>

      @auth
      <nav class="header-nav">
        <!-- 検索窓 -->
        <!-- リアルタイム検索はJSが必要なのでボタン設置しました -->
        <div class="header-nav__search">
          <form class="search-form" action="/" method="get">
            <input class="search-form__input" type="text" name="keyword" value="{{ request('keyword') }}"
              placeholder="何をお探しですか？">
            <button class="search-form__button" type="submit">検索</button>
          </form>
        </div>

        <!-- ナビゲーションリンク -->
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
      <!-- 会員登録画面とログイン画面では非表示 -->
      @if (!request()->is('login') && !request()->is('register'))
      <nav class="header-nav">
        <!-- 検索窓 -->
        <!-- リアルタイム検索はJSが必要なのでボタン設置しました -->
        <div class="header-nav__search">
          <form class="search-form" action="/" method="get">
            <input class="search-form__input" type="text" name="keyword" value="{{ request('keyword') }}"
              placeholder="何をお探しですか？">
            <button class="search-form__button" type="submit">検索</button>
          </form>
        </div>

        <!-- ナビゲーションリンク -->
        <div class="header-nav__links">
          <a class="header-nav__item header-nav__item--login" href="{{ route('login') }}">ログイン</a>
          <a class="header-nav__item header-nav__item--mypage" href="{{ route('login') }}">マイページ</a>
          <a class="header-nav__item header-nav__item--sell" href="{{ route('login') }}">出品</a>
        </div>
      </nav>
      @endif
      @endguest
    </div>
  </header>

  <div class="flash-container">
    @if (session('success') || session('error'))
    <div class="flash-message
    {{ session('success') ? 'flash-message--success' : 'flash-message--error' }}
    is-visible">
      {{ session('success') ?? session('error') }}
    </div>
    @else
    <div class="flash-message">&nbsp;</div>
    @endif
  </div>

  <main class="main">
    @yield('content')
    @yield('js')
  </main>

</body>

</html>