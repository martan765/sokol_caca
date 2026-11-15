<?php

namespace Sokol\Application\Services;

use Sokol\Application\Dto\GameTypeData;
use Sokol\Application\Support\Changes;
use Sokol\Application\ViewModels\GameTypeView;
use Sokol\Domain\Entities\GameType;
use Sokol\Domain\Exceptions\DomainRuleViolation;
use Sokol\Domain\Interfaces\Repositories\GameTypeRepositoryInterface;

class GameTypeService
{
    public function __construct(
        private readonly GameTypeRepositoryInterface $types,
    ) {}

    public function all(): array
    {
        $counts = $this->types->gameCounts();

        return array_map(
            fn (GameType $t) => new GameTypeView((int) $t->id(), $t->name(), $counts[$t->id()] ?? 0),
            $this->types->all(),
        );
    }

    public function get(int $id): GameTypeView
    {
        $type = $this->types->getById($id);

        return new GameTypeView((int) $type->id(), $type->name(), $this->types->gameCounts()[$id] ?? 0);
    }

    public function exists(int $id): bool
    {
        return $this->types->exists($id);
    }

    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        return $this->types->nameExists($name, $exceptId);
    }

    public function create(GameTypeData $data): GameTypeView
    {
        $type = $this->types->save(GameType::create($data->name));

        return new GameTypeView((int) $type->id(), $type->name());
    }

    public function update(int $id, GameTypeData $data): array
    {
        $type   = $this->types->getById($id);
        $before = ['name' => $type->name()];

        $type->rename($data->name);
        $this->types->save($type);

        return Changes::between($before, ['name' => $type->name()]);
    }

    public function delete(int $id): GameTypeView
    {
        $view = $this->get($id);

        if ($this->types->isUsed($id)) {
            throw DomainRuleViolation::gameTypeInUse($id);
        }

        $this->types->delete($id);

        return $view;
    }
}
