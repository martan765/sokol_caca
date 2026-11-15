<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GameTypeRequest;
use Psr\Log\LoggerInterface;
use Sokol\Application\Services\GameTypeService;
use Sokol\Domain\Exceptions\DomainRuleViolation;

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
        $type = $this->types->create($request->toData());

        $this->logger->info('Typ zápasu vytvořen', ['game_type_id' => $type->id, 'name' => $type->name]);

        return redirect()->route('admin.game-types.index')->with('success', 'Typ zápasu byl přidán.');
    }

    public function edit(int $gameType)
    {
        return view('admin.game-types.edit', ['type' => $this->types->get($gameType)]);
    }

    public function update(GameTypeRequest $request, int $gameType)
    {
        $changes = $this->types->update($gameType, $request->toData());

        $this->logger->info('Typ zápasu upraven', ['game_type_id' => $gameType, 'changes' => $changes]);

        return redirect()->route('admin.game-types.index')->with('success', 'Typ zápasu byl upraven.');
    }

    public function destroy(int $gameType)
    {
        try {
            $deleted = $this->types->delete($gameType);
        } catch (DomainRuleViolation) {
            $this->logger->notice('Smazání typu zápasu odmítnuto – typ je používán', ['game_type_id' => $gameType]);

            return back()->with('error', 'Tento typ používají existující zápasy, nelze ho smazat.');
        }

        $this->logger->info('Typ zápasu smazán', ['game_type_id' => $deleted->id, 'name' => $deleted->name]);

        return redirect()->route('admin.game-types.index')->with('success', 'Typ zápasu byl smazán.');
    }
}
