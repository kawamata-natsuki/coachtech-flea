<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ];
    }

    public function messages()
    {
        return [
            'rating.required' => '評価を選択してください。',
            'rating.integer'  => '評価は数値で指定してください。',
            'rating.min'      => '評価は1以上にしてください。',
            'rating.max'      => '評価は5以下にしてください。',
        ];
    }
}
