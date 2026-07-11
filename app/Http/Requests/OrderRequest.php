<?php
/**
 * @desc WebhookController
 * @author Tinywan(ShaoBo Wan)
 * @date 2026/7/10 16:24
 */
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 订单请求验证类
 *
 * 用于验证订单相关请求的参数合法性，包括姓名和邮箱字段的校验规则、
 * 自定义验证错误消息以及字段属性名称的中文映射。
 */
class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool 当前用户是否被授权发起此请求，始终返回 true 表示允许所有用户
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> 验证规则数组，键为字段名，值为对应的验证规则
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:20',
            'email' => 'required|email',
        ];
    }

    /**
     * 获取自定义的验证错误消息
     *
     * 为每个验证规则定义中文错误提示，用于替换 Laravel 默认的英文验证消息。
     *
     * @return array<string, string> 自定义验证错误消息数组，键为"字段.规则"格式，值为中文错误提示
     */
    public function messages(): array
    {
        return [
            'name.required' => '姓名必填',
            'name.min' => '姓名不能少于两个字符',
            'name.max' => '姓名不能超过二十个字符',
            'email.required' => '邮箱必填',
            'email.email' => '邮箱格式不正确',
        ];
    }

    /**
     * 获取验证字段的自定义属性名称
     *
     * 将字段名映射为中文名称，用于在验证错误消息中展示更友好的字段描述。
     *
     * @return array<string, string> 字段属性映射数组，键为字段名，值为中文属性名称
     */
    public function attributes(): array
    {
        return [
            'name' => '姓名',
            'email' => '邮箱',
        ];
    }
}
