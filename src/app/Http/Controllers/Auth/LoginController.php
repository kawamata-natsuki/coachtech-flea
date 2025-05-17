<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        // 認証成功：セッション再生成、元の画面orトップページにリダイレクト
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // 認証失敗：エラー表示＆入力内容を保持して再表示
        return back()->withErrors([
            'login' => 'ログイン情報が登録されていません',
        ])->withInput();
    }
}
