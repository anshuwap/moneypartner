<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOutletValidation extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'outlet_type'        => 'required|string|max:30',
            'outlet_name'        => 'required|string|min:2|max:30',
            'outlet_name'        => 'required|string|min:2',
            'state'              => 'required',
            'city'               => 'required',
            'pincode'            => 'required|numeric|not_in:0|digits:6',
            'incorporation_date' => 'required',
            'outlet_gst_number'  => 'required|max:30|min:2',
            'account_status'     => 'required',
            'retailer_name'      => 'required|string|min:2|max:30',
            'mobile_no'          => 'required|numeric|not_in:0|digits:10',
            'email'              => 'required|email|min:2|max:30',
            'gender'             => 'required|min:2|max:30',
            'user_type'          => 'required',
            'password'           => 'required|max:16|min:4',
            'date_of_birth'      => 'required',
            'permanent_address'  => 'required',
            'id_proff'           => 'required',
            'address_proff'      => 'required',
            'pancard'            => 'required'
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
