<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
        $this->app->singleton(UpdatesUserProfileInformation::class, UpdateUserProfileInformation::class);
    }

    public function boot(): void
    {
        // 新規ユーザー登録
        Fortify::createUsersUsing(CreateNewUser::class);

        // プロフィールの更新処理
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);

        // 新規ユーザー登録画面の表示
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // ログイン画面の表示
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // ログイン試行の回数制限（1分間に最大5回まで）
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower(
                $request->input('email') . '|' . $request->ip()
            ));

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
