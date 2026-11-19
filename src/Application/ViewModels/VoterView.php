<?php

namespace Sokol\Application\ViewModels;

final readonly class VoterView
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
