<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttendanceSheetRequest;
use App\Http\Requests\Admin\GameRequest;
use App\Models\Game;
use App\Services\AttendanceService;
use App\Services\GameService;
use Psr\Log\LoggerInterface;

class GameController extends Controller
{
    public function __construct(
        private readonly GameService $games,
        private readonly AttendanceService $attendance,
        private readonly LoggerInterface $logger,
    ) {}

    public function index()
    {
        $games = $this->games->paginated();

        return view('admin.games.index', compact('games'));
    }

    public function create()
    {
        $gameTypes = $this->games->types();

        return view('admin.games.create', compact('gameTypes'));
    }

    public function store(GameRequest $request)
    {
        $game = $this->games->schedule($request->validated());

        $this->logger->info('Zápas vytvořen', ['game_id' => $game->id, 'match' => "{$game->home_team} vs {$game->away_team}"]);

        return redirect()->route('admin.games.index')->with('success', 'Zápas byl úspěšně přidán!');
    }

    public function edit(Game $game)
    {
        $gameTypes = $this->games->types();

        return view('admin.games.edit', compact('game', 'gameTypes'));
    }

    public function update(GameRequest $request, Game $game)
    {
        $this->games->update($game, $request->validated());

        $this->logger->info('Zápas upraven', ['game_id' => $game->id, 'changes' => $game->getChanges()]);

        return redirect()->route('admin.games.index')->with('success', 'Zápas byl upraven.');
    }

    public function destroy(Game $game)
    {
        $this->games->cancel($game);

        $this->logger->info('Zápas smazán', ['game_id' => $game->id, 'match' => "{$game->home_team} vs {$game->away_team}"]);

        return redirect()->route('admin.games.index')->with('success', 'Zápas byl zrušen.');
    }

    /**
     * Trenér zapisuje docházku za hráče (např. když se omluví telefonem).
     */
    public function attendance(Game $game)
    {
        return view('admin.attendance.edit', $this->attendance->gameSheet($game) + [
            'heading' => "Docházka – {$game->home_team} vs {$game->away_team}",
            'date'    => $game->game_date,
            'action'  => route('admin.games.attendance.update', $game),
            'back'    => route('admin.games.index'),
        ]);
    }

    public function updateAttendance(AttendanceSheetRequest $request, Game $game)
    {
        $this->attendance->saveGameSheet($game, $request->validated('attendance', []));

        $this->logger->info('Docházka zápasu upravena trenérem', ['game_id' => $game->id]);

        return redirect()->route('admin.games.index')->with('success', 'Docházka byla uložena.');
    }
}
