<?php

namespace Sokol\Application\Abstraction;

use Sokol\Application\Dto\GameData;
use Sokol\Application\ViewModels\GameView;
use Sokol\Domain\Pagination\Page;

interface GameServiceInterface
{
    public function upcoming(?int $viewerId): array;

    public function paginated(int $page): Page;

    public function get(int $id): GameView;

    public function exists(int $id): bool;

    public function types(): array;

    public function schedule(GameData $data): GameView;

    public function update(int $id, GameData $data): array;

    public function cancel(int $id): GameView;
}
