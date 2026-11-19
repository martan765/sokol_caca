<?php

namespace App\Http\Requests\Admin;

use App\Rules\UniqueAttendanceStatusName;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', new UniqueAttendanceStatusName($this->routeId('attendance_status'))],
        ];
    }

    private function routeId(string $parameter): ?int
    {
        $id = $this->route($parameter);

        return $id === null ? null : (int) $id;
    }
}
