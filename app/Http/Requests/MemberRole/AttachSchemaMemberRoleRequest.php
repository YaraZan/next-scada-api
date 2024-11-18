<?php

namespace App\Http\Requests\MemberRole;

use App\Rules\BinaryUuidExists;
use Illuminate\Foundation\Http\FormRequest;

class AttachSchemaMemberRoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'member_role' => ['uuid',  new BinaryUuidExists('member_roles')],
            'schemas' => ['nullable', 'array'],
            'schemas.*' => ['uuid',  new BinaryUuidExists('schemas')]
        ];
    }
}
