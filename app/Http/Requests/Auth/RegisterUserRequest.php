<?php

namespace App\Http\Requests\Auth;

use App\Rules\UniqueEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use Sokol\Application\Dto\RegisterUserData;

class RegisterUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', new UniqueEmail()],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function toData(): RegisterUserData
    {
        return new RegisterUserData($this->validated('name'), $this->validated('email'), $this->validated('password'));
    }
}
