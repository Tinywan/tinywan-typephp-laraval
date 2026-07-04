<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|min:1',
            'name' => 'required|string|min:2|max:20',
            'email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '姓名必填',
            'email.required' => '邮箱必填',
            'email.email' => '邮箱格式不正确',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '姓名',
            'email' => '邮箱',
        ];
    }
}
