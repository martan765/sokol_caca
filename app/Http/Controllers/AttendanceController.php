<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use Sokol\Application\Abstraction\AttendanceServiceInterface;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceServiceInterface $attendance,
    ) {}

    public function store(StoreAttendanceRequest $request)
    {
        $this->attendance->vote($request->toData());

        $eventType = $request->isTrainingVote() ? 'trénink' : 'zápas';

        return back()->with('success', "Účast na {$eventType} byla uložena!");
    }
}
