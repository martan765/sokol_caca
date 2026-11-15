<?php

namespace Sokol\Infrastructure;

use Illuminate\Support\ServiceProvider;
use Sokol\Domain\Interfaces\Managers\ClockInterface;
use Sokol\Domain\Interfaces\Managers\ClubSettingsInterface;
use Sokol\Domain\Interfaces\Managers\PasswordHasherInterface;
use Sokol\Domain\Interfaces\Managers\PasswordResetBrokerInterface;
use Sokol\Domain\Interfaces\Managers\UnitOfWorkInterface;
use Sokol\Domain\Interfaces\Managers\UserEventNotifierInterface;
use Sokol\Domain\Interfaces\Managers\WeatherProviderInterface;
use Sokol\Domain\Interfaces\Repositories\AttendanceRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\AttendanceStatusRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\GameRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\GameTypeRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\TrainingRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\UserRepositoryInterface;
use Sokol\Infrastructure\Auth\LaravelPasswordHasher;
use Sokol\Infrastructure\Auth\LaravelPasswordResetBroker;
use Sokol\Infrastructure\Auth\LaravelUserEventNotifier;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentAttendanceRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentAttendanceStatusRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentGameRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentGameTypeRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentTrainingRepository;
use Sokol\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use Sokol\Infrastructure\Persistence\EloquentUnitOfWork;
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

        PasswordHasherInterface::class      => LaravelPasswordHasher::class,
        PasswordResetBrokerInterface::class => LaravelPasswordResetBroker::class,
        UserEventNotifierInterface::class   => LaravelUserEventNotifier::class,
        UnitOfWorkInterface::class          => EloquentUnitOfWork::class,
        WeatherProviderInterface::class     => OpenMeteoWeatherProvider::class,
        ClubSettingsInterface::class        => ConfigClubSettings::class,
    ];

    public $singletons = [
        ClockInterface::class => SystemClock::class,
    ];

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }
}
