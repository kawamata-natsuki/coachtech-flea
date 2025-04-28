<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    // バリデーションルール追加しています
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
            'password_confirmation' => [
                'required'
            ],
        ];
    }

    // email.email のエラーメッセージがないので、追加しています
    // 確認用パスワードの下にもエラーメッセージ表示されるように、password_confirmation.required 追加しています
    public function messages()
    {
        return [
            'name.required'     => 'お名前を入力してください',
            'email.required'        => 'メールアドレスを入力してください',
            'email.email'           => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',
            'email.unique'          => 'このメールアドレスはすでに登録されています',
            'password.required'     => 'パスワードを入力してください',
            'password.min'          => 'パスワードは8文字以上で入力してください',
            'password.confirmed'    => 'パスワードと一致しません',
            'password_confirmation.required' => '確認用パスワードを入力してください'
        ];
    }
}