<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use App\Services\GameService;

/**
 * Veřejný přehled nadcházejících zápasů s hlasováním o účasti.
 * Správa zápasů je v Admin\GameController.
 */
class GameController extends Controller
{
    public function __construct(
        private readonly GameService $games,
        private readonly AttendanceService $attendance,
    ) {}

    public function index()
    {
        $games    = $this->games->upcoming();
        $statuses = $this->attendance->statuses();

        return view('games.index', compact('games', 'statuses'));
    }
}
