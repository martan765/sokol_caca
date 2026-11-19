<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttendanceStatusRequest;
use App\Models\AttendanceStatus;
use App\Services\AttendanceService;
use Psr\Log\LoggerInterface;

/**
 * Stavy účasti jsou systémový číselník – lze je jen přejmenovat.
 */
class AttendanceStatusController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendance,
        private readonly LoggerInterface $logger,
    ) {}

    public function index()
    {
        $statuses = $this->attendance->statuses();

        return view('admin.attendance-statuses.index', compact('statuses'));
    }

    public function edit(AttendanceStatus $attendanceStatus)
    {
        return view('admin.attendance-statuses.edit', ['status' => $attendanceStatus]);
    }

    public function update(AttendanceStatusRequest $request, AttendanceStatus $attendanceStatus)
    {
        $this->attendance->renameStatus($attendanceStatus, $request->validated());

        $this->logger->info('Stav účasti přejmenován', ['status_id' => $attendanceStatus->id, 'changes' => $attendanceStatus->getChanges()]);

        return redirect()->route('admin.attendance-statuses.index')->with('success', 'Stav účasti byl přejmenován.');
    }
}
