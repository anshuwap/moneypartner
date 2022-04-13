<?php

namespace App\Http\validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;


class LoginRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
         return [
            'email'     => 'required|email|min:2|max:100',
            'password'  => 'required|min:6|max:16',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
       // throw new HttpResponseException();

     throw new HttpResponseException(response()->json([
        'status' => false,
        'message' => $validator->errors(),
    ], 400));
    }

}
