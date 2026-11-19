<?php

namespace App\Http\Requests\Admin;

use App\Rules\ExistingAttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Sokol\Application\Dto\AttendanceSheetRow;

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
            'attendance.*.status_id' => ['nullable', 'integer', new ExistingAttendanceStatus()],
            'attendance.*.note'      => ['nullable', 'string', 'max:255'],
        ];
    }

    public function rows(): array
    {
        $rows = [];

        foreach ($this->validated('attendance', []) as $userId => $row) {
            $rows[] = new AttendanceSheetRow(
                (int) $userId,
                empty($row['status_id']) ? null : (int) $row['status_id'],
                $row['note'] ?? null,
            );
        }

        return $rows;
    }
}
