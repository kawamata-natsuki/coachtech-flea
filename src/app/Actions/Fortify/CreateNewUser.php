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
        // RegisterRequest のルールとメッセージを再利用するために 手動でインスタンス化してデータを詰める
        $request = new RegisterRequest();
        $request->merge($input);

        // RegisterRequestのバリデーションルールを適用
        Validator::make(
            $request->all(),
            $request->rules(),
            $request->messages()
        )->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
