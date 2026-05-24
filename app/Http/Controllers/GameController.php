<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::where('game_date', '>=', now())
            ->orderBy('game_date', 'asc')
            ->get();

        return view('games.index', compact('games'));
    }

    public function create()
    {
        $gameTypes = DB::table('game_types')->get();

        return view('games.create', compact('gameTypes'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Nemáš právo přidávat zápasy, brácho.');
        }

        $validated = $request->validate([
            'game_date'     => 'required|date',
            'home_team'     => 'required|string|max:255',
            'away_team'     => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'match_type_id' => 'required|integer|exists:game_types,id',
            'result'        => 'nullable|string|max:50',
        ]);

        Game::create($validated);

        return redirect()->route('games.index')->with('success', 'Zápas byl úspěšně přidán!');
    }

    public function destroy(Request $request, Game $game)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'admin') {
            abort(403, 'Nemáš právo mazat zápasy, brácho.');
        }

        $game->delete();

        return redirect()->route('games.index')->with('success', 'Zápas byl zrušen.');
    }
}
