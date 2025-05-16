<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        return view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect('/mypage/profile');
    }

    public function resend(Request $request)
    {
        /** メール認証の再送信 */
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '確認メールを再送しました');
    }

    public function check()
    {
        return redirect(auth()->user()?->hasVerifiedEmail() ? '/' : '/email/verify');
    }
}
