<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;

class TrainingController extends Controller
{

    public function create()
    {
        return view('admin.trainings.create');
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'training_date' => 'required|date',
            'location'      => 'required|string|max:255',
            'description'   => 'nullable|string',
        ]);

        Training::create($validated);

        return redirect()
            ->route('admin.trainings.create')
            ->with('success', 'Trénink byl úspěšně naplánován! Teď ho uvidí i ostatní.');
    }
}
