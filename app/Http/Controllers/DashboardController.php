<?php

namespace App\Http\Controllers;

use Sokol\Application\Services\DashboardService;
use Sokol\Application\Services\UserService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard,
        private readonly UserService $users,
    ) {}

    public function index()
    {
        $userId = $this->currentUserId();

        if ($this->users->isAdmin($userId)) {
            return view('dashboard', ['isAdmin' => true, 'dashboard' => $this->dashboard->forAdmin()]);
        }

        return view('dashboard', ['isAdmin' => false, 'dashboard' => $this->dashboard->forPlayer($userId)]);
    }
}
