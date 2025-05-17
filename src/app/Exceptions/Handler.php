<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    // バリデーションエラー時に再表示しない入力項目を指定
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    // エラー発生時の画面遷移
    public function render($request, Throwable $exception)
    {
        // CSRFトークンエラー（TokenMismatchException）の処理
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()->route('login')->with('error', 'セッションの有効期限が切れました。もう一度ログインしてください。');
        }

        // その他の例外は、Laravelデフォルトのエラー表示
        return parent::render($request, $exception);
    }
}