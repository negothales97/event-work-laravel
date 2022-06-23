<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\ApiBaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskStoreRequest extends ApiBaseRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:10000',
            'admin_id' => 'sometimes|exists:admins,uuid',
            // 'user_id' => 'required|exists:users,uuid',
            // 'company_id' => 'required|exists:companies,uuid',
            'priority' => ['required', Rule::in('low', 'medium', 'high')],
        ];
    }
}
