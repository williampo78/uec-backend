<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetOrdersRequest extends FormRequest
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
            'date' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'date.required' => '日期不能為空',
            'date.date' => '日期格式錯誤',
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
