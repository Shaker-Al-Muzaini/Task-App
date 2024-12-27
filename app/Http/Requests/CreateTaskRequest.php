<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'projectId' => 'required|',
            'memberIds' => 'required|array', // تأكد من أن memberIds عبارة عن مصفوفة
            'memberIds.*' => 'numeric', // تحقق من أن كل عضو موجود في جدول users
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'memberIds.required' => 'The memberIds field is required.',
            'memberIds.array' => 'The memberIds field must be an array.',
            'memberIds.*.numeric' => 'Each memberId must be a number.',
        ];
    }
}

