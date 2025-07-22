<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatMessageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'message'       => ['required', 'max:400', 'string',],
            'chat_image'    => ['nullable', 'mimes:jpeg,png', 'image'],
        ];
    }

    public function messages()
    {
        return [
            'message.required'  => '本文を入力してください',
            'message.max'       => '本文は400文字以内で入力してください',
            'chat_image.mimes'  => '「.png」または「.jpeg」形式でアップロードしてください',
            'chat_image.image'  => '画像は画像ファイルを選択してください',
        ];
    }
}
