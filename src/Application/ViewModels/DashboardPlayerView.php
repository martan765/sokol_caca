<?php

namespace Sokol\Application\ViewModels;

final readonly class DashboardPlayerView
{
    public function __construct(
        public array $months,
    ) {}
}
