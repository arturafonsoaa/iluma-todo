<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): Response
    {
        $tasks = auth()->user()->tasks()->latest()->get();

        return Inertia::render('tasks/Index', [
            'tasks' => $tasks,
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        auth()->user()->tasks()->create($request->validated());

        return back();
    }

    public function update(Task $task): RedirectResponse
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->update([
            'completed_at' => $task->isCompleted() ? null : now(),
        ]);

        return back();
    }

    public function destroy(Task $task): RedirectResponse
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return back();
    }
}
