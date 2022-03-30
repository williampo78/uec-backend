<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetOrderDetailRequest extends FormRequest
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

    public function validationData(): array
    {
        // Retrieve the route parameters
        $route_parameters = $this
            ->route()
            ->parameters();

        // Retrieve the request data
        $request_data = $this->all();

        // Return both as data for the validation
        return array_merge($request_data, $route_parameters);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_no' => 'required|regex:/^OD[0-9]{6}[0-9a-zA-Z]{6}$/',
        ];
    }

    public function messages()
    {
        return [
            'order_no.required' => '訂單編號不能為空',
            'order_no.regex' => '訂單編號格式錯誤',
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
