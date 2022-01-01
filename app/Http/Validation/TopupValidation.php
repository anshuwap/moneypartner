<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class TopupValidation extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_mode'      => 'required|max:30',
            'payment_reference' => 'required|min:2|max:30',
            'amount'            => 'required',
            'comment'           => 'required|max:5000',
            'payment_date'      => 'required'
        ];
    }
    // public function messages()
    // {
    //     return [
    //         'payment.required' => 'Retailer Name field is Required.',
    //         'retailer_name.string'=>'Retailer Name should be string.',
    //         'retailer_name.max'=>'Retailer Name should not be maximum 30 Character.',
    //         'mobile_no.required'=>'Mobile Number field is Required.',
    //     ];
    // }

    protected function failedValidation(Validator $validator)
    {
       // throw new HttpResponseException();
     throw new HttpResponseException(response(json_encode(array('validation'=>$validator->errors()))));
    }
}
