<?php

namespace Sokol\Application\Abstraction;

use Sokol\Application\ViewModels\DashboardAdminView;
use Sokol\Application\ViewModels\DashboardPlayerView;

interface DashboardServiceInterface
{
    public function forPlayer(int $userId): DashboardPlayerView;

    public function forAdmin(): DashboardAdminView;
}
