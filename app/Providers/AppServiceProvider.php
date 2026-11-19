<?php

namespace App\Providers;

use App\Http\Controllers\Admin;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Contracts\AttendanceStatusRepositoryInterface;
use App\Repositories\Contracts\GameRepositoryInterface;
use App\Repositories\Contracts\GameTypeRepositoryInterface;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\AttendanceRepository;
use App\Repositories\Eloquent\AttendanceStatusRepository;
use App\Repositories\Eloquent\GameRepository;
use App\Repositories\Eloquent\GameTypeRepository;
use App\Repositories\Eloquent\TrainingRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Kontrakty (Domain) -> Eloquent implementace (Infrastructure)
        $this->app->bind(TrainingRepositoryInterface::class, TrainingRepository::class);
        $this->app->bind(GameRepositoryInterface::class, GameRepository::class);
        $this->app->bind(GameTypeRepositoryInterface::class, GameTypeRepository::class);
        $this->app->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
        $this->app->bind(AttendanceStatusRepositoryInterface::class, AttendanceStatusRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

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
        //
    }
}
