<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GameTypeRequest;
use App\Models\GameType;
use App\Services\GameTypeService;
use Psr\Log\LoggerInterface;

class GameTypeController extends Controller
{
    public function __construct(
        private readonly GameTypeService $types,
        private readonly LoggerInterface $logger,
    ) {}

    public function index()
    {
        $types = $this->types->all();

        return view('admin.game-types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.game-types.create');
    }

    public function store(GameTypeRequest $request)
    {
        $type = $this->types->create($request->validated());

        $this->logger->info('Typ zápasu vytvořen', ['game_type_id' => $type->id, 'name' => $type->name]);

        return redirect()->route('admin.game-types.index')->with('success', 'Typ zápasu byl přidán.');
    }

    public function edit(GameType $gameType)
    {
        return view('admin.game-types.edit', ['type' => $gameType]);
    }

    public function update(GameTypeRequest $request, GameType $gameType)
    {
        $this->types->update($gameType, $request->validated());

        $this->logger->info('Typ zápasu upraven', ['game_type_id' => $gameType->id, 'changes' => $gameType->getChanges()]);

        return redirect()->route('admin.game-types.index')->with('success', 'Typ zápasu byl upraven.');
    }

    public function destroy(GameType $gameType)
    {
        if (! $this->types->delete($gameType)) {
            $this->logger->notice('Smazání typu zápasu odmítnuto – typ je používán', ['game_type_id' => $gameType->id]);

            return back()->with('error', 'Tento typ používají existující zápasy, nelze ho smazat.');
        }

        $this->logger->info('Typ zápasu smazán', ['game_type_id' => $gameType->id, 'name' => $gameType->name]);

        return redirect()->route('admin.game-types.index')->with('success', 'Typ zápasu byl smazán.');
    }
}
