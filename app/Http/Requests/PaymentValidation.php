<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'card_holder' => 'required|string|between:3,255',
            'product' => 'required|array',
            'total' => 'required|numeric',
            'card' => 'required|size:16',
            'month' => 'required|numeric|between:1,12',
            'year' => 'required|numeric|between:2020,2031',
            'ccv' => 'required|string|between:3,4',
        ];
    }
}
