<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttendanceSheetRequest;
use App\Http\Requests\Admin\TrainingRequest;
use App\Models\Training;
use App\Services\AttendanceService;
use App\Services\TrainingService;
use Psr\Log\LoggerInterface;

class TrainingController extends Controller
{
    public function __construct(
        private readonly TrainingService $trainings,
        private readonly AttendanceService $attendance,
        private readonly LoggerInterface $logger,
    ) {}

    public function index()
    {
        $trainings = $this->trainings->paginated();

        return view('admin.trainings.index', compact('trainings'));
    }

    public function create()
    {
        return view('admin.trainings.create');
    }

    public function store(TrainingRequest $request)
    {
        $training = $this->trainings->plan($request->validated());

        $this->logger->info('Trénink vytvořen', ['training_id' => $training->id, 'date' => $training->training_date->toDateTimeString()]);

        return redirect()
            ->route('admin.trainings.index')
            ->with('success', 'Trénink byl úspěšně naplánován! Teď ho uvidí i ostatní.');
    }

    public function edit(Training $training)
    {
        return view('admin.trainings.edit', compact('training'));
    }

    public function update(TrainingRequest $request, Training $training)
    {
        $this->trainings->update($training, $request->validated());

        $this->logger->info('Trénink upraven', ['training_id' => $training->id, 'changes' => $training->getChanges()]);

        return redirect()->route('admin.trainings.index')->with('success', 'Trénink byl upraven.');
    }

    public function destroy(Training $training)
    {
        $this->trainings->cancel($training);

        $this->logger->info('Trénink smazán', ['training_id' => $training->id, 'date' => $training->training_date->toDateTimeString()]);

        return redirect()->route('admin.trainings.index')->with('success', 'Trénink byl úspěšně zrušen.');
    }

    /**
     * Trenér zapisuje docházku za hráče (např. když se omluví telefonem).
     */
    public function attendance(Training $training)
    {
        return view('admin.attendance.edit', $this->attendance->trainingSheet($training) + [
            'heading' => 'Docházka – trénink',
            'date'    => $training->training_date,
            'action'  => route('admin.trainings.attendance.update', $training),
            'back'    => route('admin.trainings.index'),
        ]);
    }

    public function updateAttendance(AttendanceSheetRequest $request, Training $training)
    {
        $this->attendance->saveTrainingSheet($training, $request->validated('attendance', []));

        $this->logger->info('Docházka tréninku upravena trenérem', ['training_id' => $training->id]);

        return redirect()->route('admin.trainings.index')->with('success', 'Docházka byla uložena.');
    }
}
