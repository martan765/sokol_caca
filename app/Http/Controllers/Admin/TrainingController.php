<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttendanceSheetRequest;
use App\Http\Requests\Admin\TrainingRequest;
use App\Http\Support\Paging;
use Psr\Log\LoggerInterface;
use Sokol\Application\Abstraction\AttendanceServiceInterface;
use Sokol\Application\Abstraction\TrainingServiceInterface;
use Sokol\Domain\Enums\EventType;

class TrainingController extends Controller
{
    public function __construct(
        private readonly TrainingServiceInterface $trainings,
        private readonly AttendanceServiceInterface $attendance,
        private readonly LoggerInterface $logger,
    ) {}

    public function index()
    {
        $trainings = Paging::toPaginator($this->trainings->paginated(Paging::currentPage()));

        return view('admin.trainings.index', compact('trainings'));
    }

    public function create()
    {
        return view('admin.trainings.create');
    }

    public function store(TrainingRequest $request)
    {
        $training = $this->trainings->plan($request->toData());

        $this->logger->info('Trénink vytvořen', ['training_id' => $training->id, 'date' => $training->date->format('Y-m-d H:i:s')]);

        return redirect()
            ->route('admin.trainings.index')
            ->with('success', 'Trénink byl úspěšně naplánován! Teď ho uvidí i ostatní.');
    }

    public function edit(int $training)
    {
        return view('admin.trainings.edit', ['training' => $this->trainings->get($training)]);
    }

    public function update(TrainingRequest $request, int $training)
    {
        $changes = $this->trainings->update($training, $request->toData());

        $this->logger->info('Trénink upraven', ['training_id' => $training, 'changes' => $changes]);

        return redirect()->route('admin.trainings.index')->with('success', 'Trénink byl upraven.');
    }

    public function destroy(int $training)
    {
        $cancelled = $this->trainings->cancel($training);

        $this->logger->info('Trénink smazán', ['training_id' => $cancelled->id, 'date' => $cancelled->date->format('Y-m-d H:i:s')]);

        return redirect()->route('admin.trainings.index')->with('success', 'Trénink byl úspěšně zrušen.');
    }

    /**
     * Trenér zapisuje docházku za hráče (např. když se omluví telefonem).
     */
    public function attendance(int $training)
    {
        $sheet = $this->attendance->sheet(EventType::Training, $training);

        return view('admin.attendance.edit', [
            'players'  => $sheet->players,
            'current'  => $sheet->current,
            'statuses' => $sheet->statuses,
            'heading'  => 'Docházka – trénink',
            'date'     => $sheet->eventDate,
            'action'   => route('admin.trainings.attendance.update', $training),
            'back'     => route('admin.trainings.index'),
        ]);
    }

    public function updateAttendance(AttendanceSheetRequest $request, int $training)
    {
        $this->attendance->saveSheet(EventType::Training, $training, $request->rows());

        $this->logger->info('Docházka tréninku upravena trenérem', ['training_id' => $training]);

        return redirect()->route('admin.trainings.index')->with('success', 'Docházka byla uložena.');
    }
}
