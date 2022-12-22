<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'max:255',
            'email'=>'email',
            'password' => 'min:8|confirmed',
            'age' => 'integer',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg'
        ];
    }
    public function  failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success'=>false,
            'message'=>'validation error',
            'errors'=>$validator->errors()
        ]));
    }
}
