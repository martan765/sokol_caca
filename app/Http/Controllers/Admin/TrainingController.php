<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    /**
     * Zobrazí formulář pro vytvoření tréninku.
     */
    public function create()
    {
        return view('admin.trainings.create');
    }

    /**
     * Uloží nový trénink do databáze.
     */
    public function store(Request $request)
    {
        // 1. Validace - nepustíme do DB nic, co tam nepatří
        $validated = $request->validate([
            'training_date' => 'required|date',
            'location'      => 'required|string|max:255',
            'description'   => 'nullable|string',
        ]);

        // 2. Vytvoření záznamu
        // (Ujisti se, že v Modelu Training máš $fillable!)
        Training::create($validated);

        // 3. Návrat zpět s úspěšnou zprávou
        return redirect()
            ->route('admin.trainings.create')
            ->with('success', 'Trénink byl úspěšně naplánován! Teď ho uvidí i ostatní.');
    }
}
