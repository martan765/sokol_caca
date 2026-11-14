<?php

namespace Sokol\Infrastructure;

use Illuminate\Support\ServiceProvider;
use Sokol\Domain\Repositories\AttendanceRepositoryInterface;
use Sokol\Domain\Repositories\AttendanceStatusRepositoryInterface;
use Sokol\Domain\Repositories\GameRepositoryInterface;
use Sokol\Domain\Repositories\GameTypeRepositoryInterface;
use Sokol\Domain\Repositories\TrainingRepositoryInterface;
use Sokol\Domain\Repositories\UserRepositoryInterface;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentAttendanceRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentAttendanceStatusRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentGameRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentGameTypeRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentTrainingRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;

class InfrastructureServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepositoryInterface::class             => EloquentUserRepository::class,
        TrainingRepositoryInterface::class         => EloquentTrainingRepository::class,
        GameRepositoryInterface::class             => EloquentGameRepository::class,
        GameTypeRepositoryInterface::class         => EloquentGameTypeRepository::class,
        AttendanceStatusRepositoryInterface::class => EloquentAttendanceStatusRepository::class,
        AttendanceRepositoryInterface::class       => EloquentAttendanceRepository::class,
    ];

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }
}
