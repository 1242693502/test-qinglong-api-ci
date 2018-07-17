<?php

namespace App\Http\Requests\InternalApi\Notification;

use App\Http\Requests\InternalApi\BaseRequest;

class NotificationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'to_uuid'     => 'required|string|max:32',
            'to_type'     => 'required',
            'from_uuid'   => 'required|string|max:32',
            'from_type'   => 'required',
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:1024',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'to_uuid'     => '接收者UUID',
            'to_type'     => '接收者类型',
            'from_uuid'   => '来源对象UUID',
            'from_type'   => '来源类型',
            'title'       => '标题',
            'description' => '描述',
        ];
    }
}