<?php
namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CancelOrderRequest extends FormRequest
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
                Rule::exists('lookup_values_v', 'code')->where('active', 1)->where('type_code', 'CANCEL_REQ_REASON'),
            ],
            'remark' => 'max:300',
        ];
    }

    public function messages()
    {
        return [
            'order_no.required' => '訂單編號不能為空',
            'order_no.regex' => '訂單編號格式錯誤',
            'code.required' => '取消原因代碼不能為空',
            'code.exists' => '取消原因代碼不存在',
            'remark.max' => '取消說明不能超過:max個字',
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
