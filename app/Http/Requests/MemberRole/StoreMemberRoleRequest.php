<?php

namespace App\Http\Requests\MemberRole;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRoleRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'],
            'description' => ['nullable', 'string'],
            'can_write_tags' => ['required', 'boolean'],
            'can_edit_tags' => ['required', 'boolean'],
            'can_create_schemas' => ['required', 'boolean'],
            'schemas' => ['required', 'array', 'exists:schemas,uuid'],
            'schemas.*' => ['uuid']
        ];
    }
}
