<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Services\AttendanceService;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendance,
    ) {}

    public function store(StoreAttendanceRequest $request)
    {
        $this->attendance->vote($request->user(), $request->validated());

        $eventType = $request->isTrainingVote() ? 'trénink' : 'zápas';

        return back()->with('success', "Účast na {$eventType} byla uložena!");
    }
}
