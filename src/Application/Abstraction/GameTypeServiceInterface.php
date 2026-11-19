<?php

namespace Sokol\Application\Abstraction;

use Sokol\Application\Dto\GameTypeData;
use Sokol\Application\ViewModels\GameTypeView;

interface GameTypeServiceInterface
{
    public function all(): array;

    public function get(int $id): GameTypeView;

    public function exists(int $id): bool;

    public function nameExists(string $name, ?int $exceptId = null): bool;

    public function create(GameTypeData $data): GameTypeView;

    public function update(int $id, GameTypeData $data): array;

    public function delete(int $id): GameTypeView;
}
