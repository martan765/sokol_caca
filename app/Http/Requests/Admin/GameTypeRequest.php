<?php

namespace App\Http\Requests\Admin;

use App\Rules\UniqueGameTypeName;
use Illuminate\Foundation\Http\FormRequest;
use Sokol\Application\Dto\GameTypeData;

class GameTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('game_type');

        return [
            'name' => ['required', 'string', 'max:50', new UniqueGameTypeName($id === null ? null : (int) $id)],
        ];
    }

    public function toData(): GameTypeData
    {
        return new GameTypeData($this->validated('name'));
    }
}
