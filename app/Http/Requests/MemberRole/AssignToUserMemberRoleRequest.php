<?php

namespace App\Http\Requests\MemberRole;

use App\Rules\BinaryUuidExists;
use App\Rules\GuestOrCustomMemberRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignToUserMemberRoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user' => ['required', 'uuid', new BinaryUuidExists('users')],
            'member_role' => ['required', new GuestOrCustomMemberRole],
            'workspace' => ['required', 'uuid', new BinaryUuidExists('workspaces')]
        ];
    }
}
