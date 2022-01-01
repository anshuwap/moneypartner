<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class OTPValidation extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'otp'              => 'required|numeric|digits:6|not_in:0'
        ];
    }
    public function messages()
    {
        return [
            'otp.required' => 'Please Enter OTP.',
            'otp.numeric'  => 'OTP must be numeric.',
            'otp.digirs'   => 'OTP contain only 6 digits.',
            'otp.not_in'   => 'Please Enter Valid OTP.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
       // throw new HttpResponseException();
     throw new HttpResponseException(response(json_encode(array('validation'=>$validator->errors()))));
    }
}
