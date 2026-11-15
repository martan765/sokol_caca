<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttendanceSheetRequest;
use App\Http\Requests\Admin\GameRequest;
use App\Http\Support\Paging;
use Psr\Log\LoggerInterface;
use Sokol\Application\Services\AttendanceService;
use Sokol\Application\Services\GameService;
use Sokol\Domain\Enums\EventType;

class GameController extends Controller
{
    public function __construct(
        private readonly GameService $games,
        private readonly AttendanceService $attendance,
        private readonly LoggerInterface $logger,
    ) {}

    public function index()
    {
        $games = Paging::toPaginator($this->games->paginated(Paging::currentPage()));

        return view('admin.games.index', compact('games'));
    }

    public function create()
    {
        $gameTypes = $this->games->types();

        return view('admin.games.create', compact('gameTypes'));
    }

    public function store(GameRequest $request)
    {
        $game = $this->games->schedule($request->toData());

        $this->logger->info('Zápas vytvořen', ['game_id' => $game->id, 'match' => $game->label]);

        return redirect()->route('admin.games.index')->with('success', 'Zápas byl úspěšně přidán!');
    }

    public function edit(int $game)
    {
        return view('admin.games.edit', [
            'game'      => $this->games->get($game),
            'gameTypes' => $this->games->types(),
        ]);
    }

    public function update(GameRequest $request, int $game)
    {
        $changes = $this->games->update($game, $request->toData());

        $this->logger->info('Zápas upraven', ['game_id' => $game, 'changes' => $changes]);

        return redirect()->route('admin.games.index')->with('success', 'Zápas byl upraven.');
    }

    public function destroy(int $game)
    {
        $cancelled = $this->games->cancel($game);

        $this->logger->info('Zápas smazán', ['game_id' => $cancelled->id, 'match' => $cancelled->label]);

        return redirect()->route('admin.games.index')->with('success', 'Zápas byl zrušen.');
    }

    /**
     * Trenér zapisuje docházku za hráče (např. když se omluví telefonem).
     */
    public function attendance(int $game)
    {
        $sheet = $this->attendance->sheet(EventType::Game, $game);

        return view('admin.attendance.edit', [
            'players'  => $sheet->players,
            'current'  => $sheet->current,
            'statuses' => $sheet->statuses,
            'heading'  => "Docházka – {$sheet->eventLabel}",
            'date'     => $sheet->eventDate,
            'action'   => route('admin.games.attendance.update', $game),
            'back'     => route('admin.games.index'),
        ]);
    }

    public function updateAttendance(AttendanceSheetRequest $request, int $game)
    {
        $this->attendance->saveSheet(EventType::Game, $game, $request->rows());

        $this->logger->info('Docházka zápasu upravena trenérem', ['game_id' => $game]);

        return redirect()->route('admin.games.index')->with('success', 'Docházka byla uložena.');
    }
}
