<?php

namespace App\Http\Requests\Admin;

use App\Models\GameType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GameTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', Rule::unique(GameType::class)->ignore($this->route('game_type'))],
        ];
    }
}
