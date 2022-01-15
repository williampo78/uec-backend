<?php

namespace App\Http\Requests\api;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => '會員密碼不能為空',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // 取得錯誤資訊
        $errors = $validator->errors();

        $response = response()->json([
            'message' => '參數錯誤',
            'errors' => $errors,
        ], 400);

        throw new HttpResponseException($response);
    }
}
