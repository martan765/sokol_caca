<?php

namespace Tests\Fakes;

use Sokol\Application\Ports\UnitOfWork;

final class RecordingUnitOfWork implements UnitOfWork
{
    public int $transactions = 0;

    public function transactional(callable $work): mixed
    {
        $this->transactions++;

        return $work();
    }
}
