<?php

namespace App\Http\Controllers;

use App\Enums\ProjectColor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', Rule::enum(ProjectColor::class)],
        ]);

        auth()->user()->projects()->create($validated);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Projeto criado com sucesso!',
        ]);

        return back();
    }
}
