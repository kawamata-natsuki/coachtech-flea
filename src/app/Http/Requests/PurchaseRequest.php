<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Constants\PaymentMethodConstants;

class PurchaseRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_method' => ['required', 'string', Rule::in(PaymentMethodConstants::all())],
            'postal_code' => ['required', 'string'],
            'address' => ['required', 'string'],
            'building' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払方法を選択してください',
            'payment_method.in' => '支払方法の選択内容が不正です',
            'postal_code.required' => '郵便番号が未入力です。配送先を確認してください。',
            'address.required' => '住所が未入力です。配送先を確認してください。',
        ];
    }
}
