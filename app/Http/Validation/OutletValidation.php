<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class OutletValidation extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'retailer_name' => 'required|string|max:30',
            'mobile_no' =>'required'
        ];
    }
    public function messages()
    {
        return [
            'retailer_name.required' => 'Retailer Name field is Required.',
            'retailer_name.string'=>'Retailer Name should be string.',
            'retailer_name.max'=>'Retailer Name should not be maximum 30 Character.',
            'mobile_no.required'=>'Mobile Number field is Required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
       // throw new HttpResponseException();
     throw new HttpResponseException(response(json_encode(array('validation'=>$validator->errors()))));
    }
}
