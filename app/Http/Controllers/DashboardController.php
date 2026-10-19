<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return view('dashboard', ['isAdmin' => true] + $this->dashboard->forAdmin());
        }

        return view('dashboard', ['isAdmin' => false] + $this->dashboard->forPlayer($user));
    }
}
