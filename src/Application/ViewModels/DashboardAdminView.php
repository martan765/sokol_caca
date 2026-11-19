<?php

namespace Sokol\Application\ViewModels;

final readonly class DashboardAdminView
{
    public function __construct(
        public array $months,
    ) {}
}
