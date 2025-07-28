<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{
    // メール認証画面の表示
    public function notice()
    {
        return view('auth.verify-email');
    }

    // メール認証の処理
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('profile.edit');
    }

    // 認証メール再送信
    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', '認証メールを再送信しました。メールをご確認ください。');
    }

    // メール認証済みか確認してリダイレクト
    public function check()
    {
        $user = auth()->user();

        return optional($user)->hasVerifiedEmail()
            ? redirect('/')
            : redirect()->away('https://mailtrap.io/');
    }
}
