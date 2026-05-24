<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|integer',
            'training_id' => 'nullable|exists:trainings,id',
            'game_id' => 'nullable|exists:games,id',
        ]);

        $user = $request->user();

        if ($request->filled('training_id')) {
            $user->trainings()->syncWithoutDetaching([
                $request->training_id => ['status_id' => $request->status]
            ]);
            $eventType = 'trénink';
        } elseif ($request->filled('game_id')) {
            $user->games()->syncWithoutDetaching([
                $request->game_id => ['status_id' => $request->status]
            ]);
            $eventType = 'zápas';
        }

        return back()->with('success', "Účast na {$eventType} byla uložena!");
    }
}
