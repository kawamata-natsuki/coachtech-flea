<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ItemCommentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// -----------------------------------------------------
// 認証関係のルート
// -----------------------------------------------------

// ユーザー登録の処理
Route::post('/register', [RegisterController::class, 'store'])->middleware(['guest']);
// ログイン処理
Route::post('/login', [LoginController::class, 'login']);
// メール認証
Route::middleware('auth')->prefix('email/verify')->group(function () {
    Route::get('/', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/resend', [EmailVerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('/check', [EmailVerificationController::class, 'check'])->name('verification.check');
});

// -----------------------------------------------------
// 公開ページ（ゲストもOK）
// -----------------------------------------------------

// 商品一覧画面の表示
Route::get('/', [ItemController::class, 'index'])->name('items.index');
// 商品詳細画面の表示
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

// -----------------------------------------------------
// 認証＆メール認証済ユーザー専用
// -----------------------------------------------------

Route::middleware(['auth', 'verified'])->group(function () {
    // プロフィール関連
    Route::get('/mypage', [UserController::class, 'index'])->name('profile.index');
    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/profile', [UserController::class, 'update'])->name('profile.update');

    // 住所変更関連
    Route::get('/purchase/address/{item}', [UserController::class, 'editAddress'])->name('address.edit');
    Route::put('/purchase/address/{item}', [UserController::class, 'updateAddress'])->name('address.update');

    // 商品出品
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    // 商品購入
    Route::get('/purchase/{item}', [OrderController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [OrderController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/success/{item}', [OrderController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/cancel/{item}', [OrderController::class, 'cancel'])->name('purchase.cancel');
    Route::get('/purchase/invalid/{item}', [OrderController::class, 'invalid'])->name('purchase.invalid');

    // いいね・コメント機能
    Route::post('/item/{item}/favorite', [FavoriteController::class, 'toggle'])->name('item.favorite.toggle');
    Route::post('/items/{item}/comments', [ItemCommentController::class, 'store'])->name('items.comments.store');

    // レビュー機能
    Route::post('/orders/{order}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // 取引チャット機能
    Route::get('/orders/{order}/chat', [ChatMessageController::class, 'index'])->name('chat.index');
    Route::post('/orders/{order}/chat', [ChatMessageController::class, 'store'])->name('chat.store');
    Route::put('/chat/{message}', [ChatMessageController::class, 'update'])->name('chat.update');
    Route::delete('/chat/{message}', [ChatMessageController::class, 'destroy'])->name('chat.destroy');

    // 取引完了
    Route::put('/orders/{order}/complete', [OrderController::class, 'complete'])->name('order.complete');
});
