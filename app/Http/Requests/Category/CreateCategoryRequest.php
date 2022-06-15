<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\ApiBaseRequest;

class CreateCategoryRequest extends ApiBaseRequest
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
            'name' => 'required|string',
            'color' => 'required|string',
            'parent_id' => 'sometimes|exists:categories,id',
            'status' => 'required|integer'
        ];
    }
}
