<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class OfflinePayoutValidation extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        return [
            'base_url'        => 'required',
            'amount'          => 'required|numeric|not_in:0',
            'beneficiary_name'   => 'required|string|min:2|max:30',
            // 'payment_mode'    => 'required|in:bank_account,upi',
            'payment_channel' => 'required|array',
            'payment_channel.bank_name'      => 'required|min:2|max:200',
            'payment_channel.account_number' => 'required|numeric',
            'payment_channel.ifsc_code'      => 'required|min:3|max:20'
        ];
    }

    public function messages()
    {
        return [
            'payment_mode.in' => 'Payment Mode should be `bank_account` or `upi`.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // throw new HttpResponseException();
        throw new HttpResponseException(response(json_encode(array('validation' => $validator->errors()))));
    }
}
