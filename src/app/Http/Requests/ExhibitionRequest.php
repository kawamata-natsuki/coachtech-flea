<?php

namespace App\Http\Requests;

use App\Constants\CategoryConstants;
use App\Constants\ConditionConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'max:40', 'string'],
            'description' => ['required', 'max:255', 'string'],
            'item_image' => ['required', 'mimes:jpg,jpeg,png', 'image'],
            'category_codes' => ['required', 'array'],
            'category_codes.*' => ['required', 'distinct', Rule::in(CategoryConstants::all()), 'string'],
            'condition_code' => ['required', Rule::in(ConditionConstants::all()), 'string'],
            'price' => ['required', 'integer', 'min:0', 'max:9999999'],
            'brand' => ['nullable', 'max:100', 'string']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'name.max' => '商品名は40文字以内で入力してください',
            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明は255文字以内で入力してください',
            'item_image.required' => '商品画像をアップロードしてください',
            'item_image.mimes' => '商品画像は.jpgまたは.png形式でアップロードしてください',
            'item_image.image' => '商品画像は画像ファイルを選択してください',
            'category_codes.required' => 'カテゴリーを1つ以上選択してください',
            'category_codes.*.distinct' => 'カテゴリーが重複しています',
            'category_codes.*.in' => '選択されたカテゴリーが不正です',
            'condition_code.required' => '商品の状態を選択してください',
            'condition_code.in' => '選択された商品の状態が不正です',
            'price.required' => '商品価格を入力してください',
            'price.integer' => '商品価格は数字で入力してください',
            'price.min' => '商品価格は0円以上で入力してください',
            'price.max' => '商品価格は9,999,999円以内で入力してください',
            'brand.max' => 'ブランド名は100文字以内で入力してください',
        ];
    }
}
