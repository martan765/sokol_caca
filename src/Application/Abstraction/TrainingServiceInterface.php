<?php

namespace Sokol\Application\Abstraction;

use Sokol\Application\Dto\TrainingData;
use Sokol\Application\ViewModels\TrainingView;
use Sokol\Domain\Pagination\Page;

interface TrainingServiceInterface
{
    public function upcoming(?int $viewerId): array;

    public function paginated(int $page): Page;

    public function get(int $id): TrainingView;

    public function exists(int $id): bool;

    public function plan(TrainingData $data): TrainingView;

    public function update(int $id, TrainingData $data): array;

    public function cancel(int $id): TrainingView;
}
