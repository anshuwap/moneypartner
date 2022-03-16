<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PinValidation extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
            return [
                'old_pin'    => 'required|digits:4|numeric',
                'new_pin'    => 'required|digits:4|numeric',
            ];
    }
    public function messages()
    {
        return [
            'old_pin.required'   => 'Old Pin Required.',
            'new_pin.required'   => 'New Pin Required.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // throw new HttpResponseException();
        throw new HttpResponseException(response(json_encode(array('validation' => $validator->errors()))));
    }
}
