<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateEmployeeValidation extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
            return [
                'full_name'       => 'required|string|max:30',
                'mobile_no'       => 'required|numeric|digits:10',
                'address'         => 'nullable|string|max:1000',
                'email'           => 'required|email|max:30',
                'gender'          => 'required',
            ];
    }


    protected function failedValidation(Validator $validator)
    {
        // throw new HttpResponseException();
        throw new HttpResponseException(response(json_encode(array('validation' => $validator->errors()))));
    }
}
