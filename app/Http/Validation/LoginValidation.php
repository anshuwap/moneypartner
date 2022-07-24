<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class LoginValidation extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'     => 'required|email',
            'password'  => 'required|min:6|max:16',
            'captcha'   => 'required|captcha'
        ];
    }
    public function messages()
    {
        return [
            'captcha.required'=> 'Captcha field is required.',
            'captcha.captcha'=> 'Invalid Captcha Code.'
        ];
    }

    // protected function failedValidation(Validator $validator)
    // {
    //    throw new HttpResponseException();
    // //  throw new HttpResponseException(response(json_encode(array('validation'=>$validator->errors()))));
    // }
}
