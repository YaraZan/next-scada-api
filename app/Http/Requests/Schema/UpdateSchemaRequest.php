<?php

namespace App\Http\Requests\Schema;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchemaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('schemas')->where(function ($query) {
                    return $query->where('workspace_id', $this->workspace_id);
                })
            ],
        ];
    }
}
