<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Docházka celé akce, kterou zapisuje trenér: attendance[user_id][status_id|note].
 */
class AttendanceSheetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'attendance'             => ['array'],
            'attendance.*.status_id' => ['nullable', 'integer', 'exists:attendance_statuses,id'],
            'attendance.*.note'      => ['nullable', 'string', 'max:255'],
        ];
    }
}
