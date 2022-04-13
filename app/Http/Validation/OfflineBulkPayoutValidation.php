<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class OfflineBulkPayoutValidation extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        return [
            '*.base_url'         => 'required',
            '*.amount'          => 'required|numeric',
            '*.beneficiary_name'   => 'required|string|min:2|max:30',
            // '*.payment_mode'    => 'required|in:bank_account,upi',
            '*.payment_channel' => 'required|array',
            '*.payment_channel.bank_name'      => 'required|min:2|max:200',
            '*.payment_channel.account_number' => 'required|numeric',
            '*.payment_channel.ifsc_code'      => 'required|min:3|max:50'
        ];
    }

    public function messages()
    {
        return [
            '*.amount.required'         => 'Amount filed is Required.',
            '*.amount.numeric'          => 'Amount filed should be numeric.',
            '*.receiver_name.requored'  => 'Receiver Name field is Required',
            '*.receiver_name.string'    => 'Receiver Name should be string',
            '*.receiver_name.min'       => 'Receiver Name should be min 2 Character.',
            '*.receiver_name.max'       => 'Receiver Name should not be greater then 30 Character.',
            '*.payment_mode.required'   => 'Payment Mode should be Required.',
            '*.payment_mode.in'         => 'Payment Mode should be `bank_account` or `upi`.',
            '*.payment_channel.required'=> 'Payment Channel filed is Required.',
            '*.payment_channel.array'   => 'Payment Channel should be array.',
            '*.payment_channel.bank_name.required'      => 'Bank Name field is Required',
            '*.payment_channel.bank_name.min'           => 'Bank Name should be min 2 Character.',
            '*.payment_channel.bank_name.max'           => 'Bank Name should not be greater then 200 Character',
            '*.payment_channel.account_number.required' => 'Account Number filed is Required',
            '*.payment_channel.account_number.numeric'  => 'Account Number should be Numeric.',
            '*payment_channel.ifsc_code.required'       =>'IFSC Code filed is Required',
            '*payment_channel.ifsc_code.min'            =>'IFSC Code min 3 Character.',
            '*payment_channel.ifsc_code.max'            =>'IFSC Code should not be greater then 50 Character.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // throw new HttpResponseException();
        throw new HttpResponseException(response(json_encode(array('validation' => $validator->errors()))));
    }
}
