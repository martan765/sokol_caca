<?php

namespace App\Http\Requests\Admin;

use App\Rules\UniqueEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Sokol\Application\Dto\AdminUpdateUserData;
use Sokol\Domain\Enums\Role;

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
        $userId = (int) $this->route('user');

        // Trenér si nemůže sám odebrat roli admina
        $roles = $userId === (int) Auth::id()
            ? [Role::Admin->value]
            : [Role::Admin->value, Role::Player->value];

        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', new UniqueEmail($userId)],
            'role'  => ['required', Rule::in($roles)],
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Sám sobě nemůžeš odebrat roli trenéra.',
        ];
    }

    public function toData(): AdminUpdateUserData
    {
        return new AdminUpdateUserData(
            $this->validated('name'),
            $this->validated('email'),
            Role::from($this->validated('role')),
        );
    }
}
