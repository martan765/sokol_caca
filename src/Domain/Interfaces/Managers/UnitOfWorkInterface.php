<?php

namespace Sokol\Domain\Interfaces\Managers;

interface UnitOfWorkInterface
{
    public function transactional(callable $work): mixed;
}
