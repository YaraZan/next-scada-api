<?php

namespace App\Http\Requests\Workspace;

use App\Models\Workspace;
use App\ProtocolEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreWorkspaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Workspace::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'protocol' => ['required', Rule::enum(ProtocolEnum::class)],
            'name' => ['required', 'min:3', 'max:255'],
            'opc_name' => ['required', 'max:255'],
            'connection_string' => ['required', 'max:255'],
            'host' => ['max:255'],
        ];
    }
}
