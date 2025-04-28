<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        // FormRequestはコントローラーに自動で注入されるけど、FortifyのCreateNewUserではその仕組みが使えないので、RegisterRequestのインスタンスを手動で作る必要がある
        $request = new RegisterRequest();
        // 本来FormRequestはHTTPリクエストから自動で入力データを受け取るが、手動でnewしただけでは中身は空のため、marge()を使ってFormRequestの中に$inputの内容を疑似的にセット
        $request->merge($input);

        // FormRequestに定義されたルールとメッセージを適用して、$inputに対してバリデーションを行う
        Validator::make(
            $request->all(),
            $request->rules(),
            $request->messages()
        )->validate();

        # ユーザーの作成（DB保存）
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}