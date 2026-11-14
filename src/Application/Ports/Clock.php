<?php

namespace Sokol\Application\Ports;

use DateTimeImmutable;

interface Clock
{
    public function now(): DateTimeImmutable;
}
