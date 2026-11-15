<?php

namespace App\Http\Controllers;

use Sokol\Application\Abstraction\DashboardServiceInterface;
use Sokol\Application\Abstraction\UserServiceInterface;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardServiceInterface $dashboard,
        private readonly UserServiceInterface $users,
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
