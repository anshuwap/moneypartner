<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class BankChargesValidation extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'from_amount'   => 'required|numeric',
            'to_amount'     => 'required|numeric',
            'type'          => 'required',
            'charges'       => 'required'
        ];
    }
    public function messages()
    {
        return [
            'form_account.required' =>'From Account field is Required.',
            'to_account.required'   =>'To Account field is Required.',
            'type.required'         =>'Type field is Required.',
            'charges.required'      =>'Charges field is Requierd.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
       // throw new HttpResponseException();
     throw new HttpResponseException(response(json_encode(array('validation'=>$validator->errors()))));
    }
}
