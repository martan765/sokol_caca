<?php

namespace Sokol\Application\Dto;

use Sokol\Domain\Enums\Role;

final readonly class AdminUpdateUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public Role $role,
    ) {}
}
