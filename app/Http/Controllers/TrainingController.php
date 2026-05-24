<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Training::where('training_date', '>=', now())
            ->orderBy('training_date', 'asc')
            ->get();

        return view('trainings.index', compact('trainings'));
    }

    public function destroy(Request $request, Training $training)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'admin') {
            abort(403, 'Nemáš právo mazat tréninky, brácho.');
        }

        $training->delete();

        return redirect()->route('dashboard')->with('success', 'Trénink byl úspěšně zrušen.');
    }
}
