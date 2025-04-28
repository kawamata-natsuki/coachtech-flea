<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // バリデーションルールは拡張子の指示のみ
        return [
            'profile_image' => ['file', 'image', 'mimes:jpg,jpeg,png'],
        ];
    }

    public function messages()
    {
        return [
            'profile_image.image' => 'プロフィール画像は画像ファイルを選択してください',
            'profile_image.mimes' => 'プロフィール画像は.jpgまたは.png形式でアップロードしてください',
        ];
    }
}
