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
            'postal_code.required' => '配送先を入力してください。',
            'address.required' => '配送先を入力してください。',
        ];
    }
}
