<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AccessTokenRequest extends StrictFormRequest
{
    public function rules(): array
    {
        $abilities = $this->user()->isAdmin()
            ? ['catalog:read', 'catalog:write', 'operations:read']
            : ['catalog:read', 'orders:read'];

        return [
            'name' => ['required', 'string', 'max:80'],
            'abilities' => ['required', 'array', 'min:1', 'max:3'],
            'abilities.*' => ['required', 'string', 'distinct', Rule::in($abilities)],
            'days' => ['required', 'integer', 'between:1,30'],
            'password' => ['required', 'string', 'current_password:web'],
        ];
    }
}
