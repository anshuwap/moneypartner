<?php

namespace App\Http\Validation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProfileValidation extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        if (!empty($request->old_password)) {
            return [
                'retailer_name'   => 'required|string|max:30',
                'mobile_no'       => 'required|numeric|digits:10',
                'alternate_number'=> 'nullable|numeric|digits:10',
                'email'           => 'required|email|max:30',
                'gender'          => 'required',
                'old_password'    => 'required|min:4|max:16',
                'new_password'    => 'required|max:16|min:6|same:confirm_password',
                'confirm_password'=> 'required|max:16|min:6',
            ];
        } else {
            return [
                'retailer_name'   => 'required|string|max:30',
                'mobile_no'       => 'required|numeric|digits:10',
                'alternate_number' => 'nullable|numeric|digits:10',
                'email'            => 'required|email|max:30',
                'gender'           => 'required'
            ];
        }
    }
    public function messages()
    {
        return [
            'retailer_name.required'  => 'Retailer Name field is Required.',
            'retailer_name.string'    => 'Retailer Name should be string.',
            'retailer_name.max'       => 'Retailer Name should not be maximum 30 Character.',
            'mobile_no.required'      => 'Mobile Number field is Required.',
            'mobile_no.numeric'       => 'Mobile Number Must be numeric value.',
            'mobile_no.digits'        => 'Mobile Number Must be 10 digits.',
            'alternate_number.numeric'=> 'Alternate Number Must be numeric value.',
            'alternate_number.digits' => 'Alternate Number Must be 10 digits.',
            'gender.required'         => 'Gender is Required.',
            'old_password.required'   => 'Old Password Required.',
            'old_password.max'        => 'Old Password should not be maximum 16 Character.',
            'old_password.min'        => 'Old Password should not be minimum 6 Character.',
            'new_password.required'   => 'New Password Required.',
            'new_password.max'        => 'New Password should not be maximum 16 Character.',
            'new_password.min'        => 'New Password should not be minimum 6 Character.',
            'new_password.same'       => 'New Password should be same as confirm Password.',
            'confirm_password.required'=> 'Confirm Password Required.',
            'confirm_password.max'    => 'Confirm Password should not be maximum 16 Character.',
            'confirm_password.min'    => 'Confirm Password should not be minimum 6 Character.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // throw new HttpResponseException();
        throw new HttpResponseException(response(json_encode(array('validation' => $validator->errors()))));
    }
}
