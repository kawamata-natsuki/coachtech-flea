<?php

use App\Http\Controllers\ItemController;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ItemCommentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// -----------------------------------------------------
// 認証関係のルート
// -----------------------------------------------------

// ユーザー登録の処理
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware(['guest']);
// 登録後のメール認証機能のルーティング後ほど記述する

// ログイン処理
Route::post('/login', function (LoginRequest $request) {
    $credentials = $request->only('email', 'password');

    // ログイン成功時の処理
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    // ログイン失敗時の処理
    return back()->withErrors([
        'login' => 'ログイン情報が登録されていません',
    ])->withInput();
});

// -----------------------------------------------------
// プロフィール画面のルート
// -----------------------------------------------------

Route::middleware(['auth'])->group(function () {
    Route::get('/mypage', [UserController::class, 'index'])->name('profile.index');
    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/profile', [UserController::class, 'update'])->name('profile.update');
});

// -----------------------------------------------------
// 住所変更画面のルート
// -----------------------------------------------------

Route::middleware('auth')->group(function () {
    Route::get('/purchase/address/{item}', [UserController::class, 'editAddress'])->name('address.edit');
    Route::put('/purchase/address/{item}', [UserController::class, 'updateAddress'])->name('address.update');
});

// -----------------------------------------------------
// 商品出品に関するルート
// -----------------------------------------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
});

// -----------------------------------------------------
// 商品一覧画面のルーティング
// -----------------------------------------------------

Route::get('/', [ItemController::class, 'index'])->name('items.index');

// -----------------------------------------------------
// 商品詳細画面のルーティング
// -----------------------------------------------------

// 商品詳細画面の表示
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

// いいね機能
Route::middleware('auth')->group(function () {
    Route::post('/item/{item}/favorite', [FavoriteController::class, 'toggle'])->name('item.favorite.toggle');
});

// コメント機能
Route::middleware('auth')->post('/items/{item}/comments', [ItemCommentController::class, 'store'])->name('items.comments.store');

// -----------------------------------------------------
// 商品購入画面のルーティング
// -----------------------------------------------------

Route::middleware('auth')->get('/purchase/{item}', [OrderController::class, 'show'])->name('purchase.show');

Route::middleware('auth')->post('/purchase/{item}', [OrderController::class, 'store'])->name('purchase.store');

Route::get('/purchase/success/{item}', [OrderController::class, 'success'])->name('purchase.success');
