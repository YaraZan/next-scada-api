<?php

namespace App\Http\Requests\MemberRole;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'workspace' => ['required', 'uuid', 'exists:workspaces,uuid'],
            'name' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'],
            'description' => ['nullable', 'string'],
            'can_write_tags' => ['nullable', 'boolean'],
            'can_edit_tags' => ['nullable', 'boolean'],
            'can_create_schemas' => ['nullable', 'boolean'],
            'schemas' => ['nullable', 'array', 'exists:schemas,uuid'],
            'schemas.*' => ['uuid']
        ];
    }
}
