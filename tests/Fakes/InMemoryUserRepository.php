<?php

namespace Tests\Fakes;

use Sokol\Domain\Entities\User;
use Sokol\Domain\Pagination\Page;
use Sokol\Domain\Repositories\UserRepositoryInterface;

final class InMemoryUserRepository extends InMemoryRepository implements UserRepositoryInterface
{
    protected function entityName(): string
    {
        return 'Uživatel';
    }

    public function findById(int $id): ?User
    {
        return $this->findEntity($id);
    }

    public function getById(int $id): User
    {
        return $this->getEntity($id);
    }

    public function findByIds(array $ids): array
    {
        $result = [];
        foreach ($ids as $id) {
            if ($user = $this->findById($id)) {
                $result[$id] = $user;
            }
        }

        return $result;
    }

    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        foreach ($this->items as $id => $user) {
            if ($user->email() === $email && $id !== $exceptId) {
                return true;
            }
        }

        return false;
    }

    public function players(): array
    {
        $players = array_values(array_filter($this->values(), fn (User $u) => $u->isPlayer()));
        usort($players, fn (User $a, User $b) => strcmp($a->name(), $b->name()));

        return $players;
    }

    public function paginate(int $page, int $perPage): Page
    {
        $users = $this->values();
        usort($users, fn (User $a, User $b) => [$a->role()->value, $a->name()] <=> [$b->role()->value, $b->name()]);

        return $this->pageOf($users, $page, $perPage);
    }

    public function save(User $user): User
    {
        return $this->store($user);
    }
}
