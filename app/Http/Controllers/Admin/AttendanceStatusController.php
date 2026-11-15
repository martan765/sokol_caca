<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttendanceStatusRequest;
use Psr\Log\LoggerInterface;
use Sokol\Application\Services\AttendanceService;

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

    public function edit(int $attendanceStatus)
    {
        return view('admin.attendance-statuses.edit', ['status' => $this->attendance->getStatus($attendanceStatus)]);
    }

    public function update(AttendanceStatusRequest $request, int $attendanceStatus)
    {
        $changes = $this->attendance->renameStatus($attendanceStatus, $request->validated('name'));

        $this->logger->info('Stav účasti přejmenován', ['status_id' => $attendanceStatus, 'changes' => $changes]);

        return redirect()->route('admin.attendance-statuses.index')->with('success', 'Stav účasti byl přejmenován.');
    }
}
