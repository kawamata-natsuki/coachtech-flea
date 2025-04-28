<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /* バリデーションエラーでフォームが再表示されたときに、値を"フラッシュしない"（=再表示しない）入力項目を指定する */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /* エラーが発生したときに、どの画面を表示するかを決める */
    public function render($request, Throwable $exception)
    {
        /* CSRFトークンエラー（TokenMismatchException）の時の処理 */
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()->route('login')->with('error', 'セッションの有効期限が切れました。もう一度ログインしてください。');
        }
        /* 上記条件に該当しない場合は、Laravelデフォルトのエラー表示 */
        return parent::render($request, $exception);
    }
}