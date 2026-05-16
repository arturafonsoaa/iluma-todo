<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $projects = auth()->user()->projects()->latest()->get();

        return Inertia::render('projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        auth()->user()->projects()->create($validated);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Projeto criado com sucesso!',
        ]);

        return back();
    }
}
