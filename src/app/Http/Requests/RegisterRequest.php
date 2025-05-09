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

    public function rules()
    {
        return [
            'name' => [
                'required',
                'max:50',
                'string',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                'string',
            ],
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'string',
            ],
            'password_confirmation' => [
                'required'
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required'         => 'お名前を入力してください',
            'name.max'              => 'お名前は50文字以内で入力してください',
            'email.required'        => 'メールアドレスを入力してください',
            'email.email'           => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',
            'email.max'             => 'メールアドレスは255文字以内で入力してください',
            'email.unique'          => 'このメールアドレスはすでに登録されています',
            'password.required'     => 'パスワードを入力してください',
            'password.min'          => 'パスワードは8文字以上で入力してください',
            'password.confirmed'    => 'パスワードと一致しません',
            'password_confirmation.required' => '確認用パスワードを入力してください'
        ];
    }
}
