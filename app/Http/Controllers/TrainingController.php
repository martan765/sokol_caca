<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Services\TrainingService;

/**
 * Veřejný přehled nadcházejících tréninků s hlasováním o účasti.
 * Správa tréninků je v Admin\TrainingController.
 */
class TrainingController extends Controller
{
    public function __construct(
        private readonly TrainingService $trainings,
        private readonly AttendanceService $attendance,
    ) {}

    public function index()
    {
        $trainings = $this->trainings->upcoming();
        $statuses  = $this->attendance->statuses();

        return view('trainings.index', compact('trainings', 'statuses'));
    }
}
