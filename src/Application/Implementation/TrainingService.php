<?php

namespace Sokol\Application\Implementation;

use Sokol\Application\Abstraction\TrainingServiceInterface;
use Sokol\Application\Dto\TrainingData;
use Sokol\Application\Support\Changes;
use Sokol\Application\ViewModels\TrainingView;
use Sokol\Domain\Entities\Training;
use Sokol\Domain\Enums\EventType;
use Sokol\Domain\Interfaces\Managers\ClockInterface;
use Sokol\Domain\Interfaces\Repositories\TrainingRepositoryInterface;
use Sokol\Domain\Pagination\Page;

class TrainingService implements TrainingServiceInterface
{
    public const PER_PAGE = 15;

    public function __construct(
        private readonly TrainingRepositoryInterface $trainings,
        private readonly VoterListBuilder $voters,
        private readonly ClockInterface $clock,
    ) {}

    public function upcoming(?int $viewerId): array
    {
        $trainings = $this->trainings->upcoming($this->clock->now());
        $votes = $this->voters->forEvents(
            EventType::Training,
            array_map(fn (Training $t) => (int) $t->id(), $trainings),
            $viewerId,
        );

        return array_map(fn (Training $t) => $this->toView($t, $votes[$t->id()] ?? null), $trainings);
    }

    public function paginated(int $page): Page
    {
        return $this->trainings->paginate($page, self::PER_PAGE)->map(fn (Training $t) => $this->toView($t));
    }

    public function get(int $id): TrainingView
    {
        return $this->toView($this->trainings->getById($id));
    }

    public function exists(int $id): bool
    {
        return $this->trainings->exists($id);
    }

    public function plan(TrainingData $data): TrainingView
    {
        $training = $this->trainings->save(Training::plan($data->date, $data->location, $data->description));

        return $this->toView($training);
    }

    public function update(int $id, TrainingData $data): array
    {
        $training = $this->trainings->getById($id);
        $before   = $this->snapshot($training);

        $training->update($data->date, $data->location, $data->description);
        $this->trainings->save($training);

        return Changes::between($before, $this->snapshot($training));
    }

    public function cancel(int $id): TrainingView
    {
        $view = $this->get($id);

        $this->trainings->delete($id);

        return $view;
    }

    private function snapshot(Training $training): array
    {
        return [
            'training_date' => $training->date(),
            'location'      => $training->location(),
            'description'   => $training->description(),
        ];
    }

    private function toView(Training $training, ?array $votes = null): TrainingView
    {
        return new TrainingView(
            (int) $training->id(),
            $training->date(),
            $training->location(),
            $training->description(),
            $training->isPastAt($this->clock->now()),
            $votes['voters'] ?? [],
            $votes['myStatusId'] ?? null,
        );
    }
}
