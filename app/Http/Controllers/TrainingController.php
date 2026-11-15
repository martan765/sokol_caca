<?php

namespace App\Http\Controllers;

use Sokol\Application\Abstraction\AttendanceServiceInterface;
use Sokol\Application\Abstraction\TrainingServiceInterface;

/**
 * Veřejný přehled nadcházejících tréninků s hlasováním o účasti.
 * Správa tréninků je v Admin\TrainingController.
 */
class TrainingController extends Controller
{
    public function __construct(
        private readonly TrainingServiceInterface $trainings,
        private readonly AttendanceServiceInterface $attendance,
    ) {}

    public function index()
    {
        $trainings = $this->trainings->upcoming($this->currentUserId());
        $statuses  = $this->attendance->statuses();

        return view('trainings.index', compact('trainings', 'statuses'));
    }
}
