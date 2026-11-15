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
use Sokol\Application\Abstraction\AccountServiceInterface;
use Sokol\Application\Abstraction\AttendanceServiceInterface;
use Sokol\Application\Abstraction\DashboardServiceInterface;
use Sokol\Application\Abstraction\GameServiceInterface;
use Sokol\Application\Abstraction\GameTypeServiceInterface;
use Sokol\Application\Abstraction\ScheduleServiceInterface;
use Sokol\Application\Abstraction\TrainingServiceInterface;
use Sokol\Application\Abstraction\UserServiceInterface;
use Sokol\Application\Abstraction\WeatherServiceInterface;
use Sokol\Application\Implementation\AccountService;
use Sokol\Application\Implementation\AttendanceService;
use Sokol\Application\Implementation\DashboardService;
use Sokol\Application\Implementation\GameService;
use Sokol\Application\Implementation\GameTypeService;
use Sokol\Application\Implementation\ScheduleService;
use Sokol\Application\Implementation\TrainingService;
use Sokol\Application\Implementation\UserService;
use Sokol\Application\Implementation\WeatherService;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        AccountServiceInterface::class    => AccountService::class,
        AttendanceServiceInterface::class => AttendanceService::class,
        DashboardServiceInterface::class  => DashboardService::class,
        GameServiceInterface::class       => GameService::class,
        GameTypeServiceInterface::class   => GameTypeService::class,
        ScheduleServiceInterface::class   => ScheduleService::class,
        TrainingServiceInterface::class   => TrainingService::class,
        UserServiceInterface::class       => UserService::class,
        WeatherServiceInterface::class    => WeatherService::class,
    ];

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
