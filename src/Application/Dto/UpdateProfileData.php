<?php

namespace Sokol\Application\Dto;

final readonly class UpdateProfileData
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}
}
