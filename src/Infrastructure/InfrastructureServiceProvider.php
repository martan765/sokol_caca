<?php

namespace Sokol\Infrastructure;

use Illuminate\Support\ServiceProvider;
use Sokol\Application\Ports\Clock;
use Sokol\Application\Ports\ClubSettings;
use Sokol\Application\Ports\PasswordHasher;
use Sokol\Application\Ports\PasswordResetBroker;
use Sokol\Application\Ports\UnitOfWork;
use Sokol\Application\Ports\UserEventNotifier;
use Sokol\Application\Ports\WeatherProvider;
use Sokol\Domain\Repositories\AttendanceRepositoryInterface;
use Sokol\Domain\Repositories\AttendanceStatusRepositoryInterface;
use Sokol\Domain\Repositories\GameRepositoryInterface;
use Sokol\Domain\Repositories\GameTypeRepositoryInterface;
use Sokol\Domain\Repositories\TrainingRepositoryInterface;
use Sokol\Domain\Repositories\UserRepositoryInterface;
use Sokol\Infrastructure\Auth\LaravelPasswordHasher;
use Sokol\Infrastructure\Auth\LaravelPasswordResetBroker;
use Sokol\Infrastructure\Auth\LaravelUserEventNotifier;
use Sokol\Infrastructure\Persistence\EloquentUnitOfWork;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentAttendanceRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentAttendanceStatusRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentGameRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentGameTypeRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentTrainingRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use Sokol\Infrastructure\System\ConfigClubSettings;
use Sokol\Infrastructure\System\SystemClock;
use Sokol\Infrastructure\Weather\OpenMeteoWeatherProvider;

class InfrastructureServiceProvider extends ServiceProvider
{
    public $bindings = [
        UserRepositoryInterface::class             => EloquentUserRepository::class,
        TrainingRepositoryInterface::class         => EloquentTrainingRepository::class,
        GameRepositoryInterface::class             => EloquentGameRepository::class,
        GameTypeRepositoryInterface::class         => EloquentGameTypeRepository::class,
        AttendanceStatusRepositoryInterface::class => EloquentAttendanceStatusRepository::class,
        AttendanceRepositoryInterface::class       => EloquentAttendanceRepository::class,

        PasswordHasher::class      => LaravelPasswordHasher::class,
        PasswordResetBroker::class => LaravelPasswordResetBroker::class,
        UserEventNotifier::class   => LaravelUserEventNotifier::class,
        UnitOfWork::class          => EloquentUnitOfWork::class,
        WeatherProvider::class     => OpenMeteoWeatherProvider::class,
        ClubSettings::class        => ConfigClubSettings::class,
    ];

    public $singletons = [
        Clock::class => SystemClock::class,
    ];

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }
}
