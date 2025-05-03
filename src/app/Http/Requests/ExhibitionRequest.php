<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:40'],
            'description' => ['required', 'max:255'],
            'item_image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png'],
            'category_codes' => ['required', 'array'],
            'category_codes.*' => ['required', 'distinct', 'string'],
            'condition_code' => ['required'],
            'price' => ['required', 'integer', 'min:0', 'max:9999999'],
            'brand' => ['nullable', 'string', 'max:100']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'name.string' => '商品名は文字列で入力してください',
            'name.max' => '商品名は40文字以内で入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'item_image.required' => '商品画像をアップロードしてください',
            'item_image.file' => '商品画像はファイル形式でアップロードしてください',
            'item_image.image' => '商品画像は画像ファイルを選択してください',
            'item_image.mimes' => '商品画像は.jpgまたは.png形式でアップロードしてください',
            'category_codes.required' => 'カテゴリーを1つ以上選択してください',
            'category_codes.*.required' => 'カテゴリーを1つ以上選択してください',
            'category_codes.*.distinct' => 'カテゴリーが重複しています',
            'condition_code.required' => '商品の状態を選択してください',
            'price.required' => '商品価格を入力してください',
            'price.integer' => '商品価格は数字で入力してください',
            'price.min' => '商品価格は0円以上で入力してください',
            'price.max' => '商品価格は9,999,999円以内で入力してください',
            'brand.string' => 'ブランド名は文字列で入力してください',
            'brand.max' => 'ブランド名は100文字以内で入力してください',
        ];
    }
}
