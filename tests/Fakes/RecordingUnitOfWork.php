<?php

namespace Tests\Fakes;

use Sokol\Domain\Interfaces\Managers\UnitOfWorkInterface;

final class RecordingUnitOfWork implements UnitOfWorkInterface
{
    public int $transactions = 0;

    public function transactional(callable $work): mixed
    {
        $this->transactions++;

        return $work();
    }
}
