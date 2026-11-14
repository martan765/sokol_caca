<?php

namespace Sokol\Application\Ports;

interface UnitOfWork
{
    public function transactional(callable $work): mixed;
}
