<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReturnOrderRequest extends FormRequest
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
            'code' => [
                'required',
                Rule::exists('lookup_values_v', 'code')->where('active', 1)->where('type_code', 'RETURN_REQ_REASON'),
            ],
            'remark' => 'max:300',
            'name' => 'required|max:10',
            'mobile' => 'required_without:telephone|max:10',
            'telephone' => 'required_without:mobile|required_with:telephone_ext|max:20',
            'telephone_ext' => 'max:10',
            'city' => 'required|max:10',
            'district' => 'required|max:10',
            'address' => 'required|max:255',
            'zip_code' => 'required|max:10',
        ];
    }

    public function messages()
    {
        return [
            'order_no.required' => '訂單編號不能為空',
            'order_no.regex' => '訂單編號格式錯誤',
            'code.required' => '退貨原因代碼不能為空',
            'code.exists' => '退貨原因代碼不存在',
            'remark.max' => '退貨說明不能超過:max個字',
            'name.required' => '退貨人姓名不能為空',
            'name.max' => '退貨人姓名不能超過:max個字',
            'mobile.required_without' => '退貨人手機、退貨人電話需擇一填寫',
            'mobile.max' => '退貨人手機不能超過:max個字',
            'telephone.required_without' => '退貨人手機、退貨人電話需擇一填寫',
            'telephone.required_with' => '退貨人電話不能為空',
            'telephone.max' => '退貨人電話不能超過:max個字',
            'telephone_ext.max' => '退貨人電話分機不能超過:max個字',
            'city.required' => '退貨人縣市不能為空',
            'city.max' => '退貨人縣市不能超過:max個字',
            'district.required' => '退貨人行政區不能為空',
            'district.max' => '退貨人行政區不能超過:max個字',
            'address.required' => '退貨人道路名稱不能為空',
            'address.max' => '退貨人道路名稱不能超過:max個字',
            'zip_code.required' => '退貨人郵遞區號不能為空',
            'zip_code.max' => '退貨人郵遞區號不能超過:max個字',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // 取得錯誤資訊
        $errors = $validator->errors();

        $response = response()->json([
            'message' => $errors->first() ?? '參數錯誤',
            'errors' => $errors,
        ], 400);

        throw new HttpResponseException($response);
    }
}
