<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Repositories;

use Illuminate\Database\Eloquent\Model;
use Sokol\Domain\Entities\User;
use Sokol\Domain\Enums\Role;
use Sokol\Domain\Interfaces\Repositories\UserRepositoryInterface;
use Sokol\Domain\Pagination\Page;
use Sokol\Infrastructure\Persistence\Eloquent\Mappers\UserMapper;
use Sokol\Infrastructure\Persistence\Eloquent\Models\UserModel;

final class EloquentUserRepository extends EloquentRepository implements UserRepositoryInterface
{
    protected function modelClass(): string
    {
        return UserModel::class;
    }

    protected function entityName(): string
    {
        return 'Uživatel';
    }

    protected function toEntity(Model $model): User
    {
        return UserMapper::toEntity($model);
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
        if ($ids === []) {
            return [];
        }

        $users = [];
        foreach ($this->toEntities($this->query()->whereKey($ids)) as $user) {
            $users[$user->id()] = $user;
        }

        return $users;
    }

    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        return $this->query()
            ->where('email', $email)
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->exists();
    }

    public function players(): array
    {
        return $this->toEntities($this->query()->where('role', Role::Player->value)->orderBy('name'));
    }

    public function paginate(int $page, int $perPage): Page
    {
        // 'admin' < 'player' abecedně, takže trenéři jsou nahoře
        return $this->paginateQuery($this->query()->orderBy('role')->orderBy('name'), $page, $perPage);
    }

    public function save(User $user): User
    {
        $model = $this->modelFor($user->id());

        UserMapper::fill($model, $user)->save();

        return UserMapper::toEntity($model);
    }
}
