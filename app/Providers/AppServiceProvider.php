<?php

namespace App\Providers;

use App\Http\Controllers\Admin;
use App\Policies\UserPolicy;
use App\View\Composers\CurrentUserComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Admin controllery dostanou přes DI logger zapisující do kanálu "admin" (storage/logs/admin-*.log)
        $this->app->when([
            Admin\TrainingController::class,
            Admin\GameController::class,
            Admin\UserController::class,
            Admin\GameTypeController::class,
            Admin\AttendanceStatusController::class,
        ])->needs(LoggerInterface::class)->give(fn () => Log::channel('admin'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('delete-user', [UserPolicy::class, 'delete']);

        View::composer(
            ['layouts.navigation', 'games.index', 'trainings.index', 'admin.users.index'],
            CurrentUserComposer::class,
        );
    }
}
