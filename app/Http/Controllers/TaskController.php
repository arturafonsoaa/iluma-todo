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
        $query = auth()->user()->tasks()->with('user')->latest();

        $filter = request()->query('filter', 'pending');

        if ($filter === 'completed') {
            $query->whereNotNull('completed_at');
        } else {
            $query->whereNull('completed_at');
        }

        $tasks = $query->paginate(10);

        return Inertia::render('tasks/Index', [
            'tasks' => $tasks,
            'filter' => $filter,
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
