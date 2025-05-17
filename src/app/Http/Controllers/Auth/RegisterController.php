<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request, CreateNewUser $creator)
    {
        $user = $creator->create($request->all());

        // メール認証のトリガー
        event(new Registered($user));

        // 登録したユーザーで即ログイン
        Auth::login($user);

        // セッション再生成（セキュリティ対策）
        $request->session()->regenerate();

        // 新規登録後のリダイレクト先をプロフィール編集画面に変更
        session(['profile_edit_first_time' => true]);
        return redirect()->route('profile.edit');
    }
}
