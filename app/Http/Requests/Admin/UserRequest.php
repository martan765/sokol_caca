<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Úprava uživatele trenérem. Heslo se tu záměrně nevaliduje ani neukládá.
 */
class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        // Trenér si nemůže sám odebrat roli admina
        $roles = $user->is($this->user()) ? [User::ROLE_ADMIN] : [User::ROLE_ADMIN, User::ROLE_PLAYER];

        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'role'  => ['required', Rule::in($roles)],
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Sám sobě nemůžeš odebrat roli trenéra.',
        ];
    }
}
