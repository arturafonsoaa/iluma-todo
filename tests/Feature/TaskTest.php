<?php

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests cannot access tasks page', function () {
    $response = $this->get(route('tasks.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view tasks page', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('tasks.index'));

    $response->assertOk();
});

test('authenticated users can create a task', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('tasks.store'), [
            'title' => 'My new task',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'user_id' => $user->id,
        'title' => 'My new task',
        'completed_at' => null,
    ]);
});

test('authenticated users can create a task with due date', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('tasks.store'), [
            'title' => 'Task with deadline',
            'due_date' => '2026-06-15',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'user_id' => $user->id,
        'title' => 'Task with deadline',
        'due_date' => '2026-06-15',
    ]);
});

test('task creation requires a title', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('tasks.store'), [
            'title' => '',
        ]);

    $response->assertSessionHasErrors('title');
});

test('authenticated users can toggle task completion', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.update', $task));

    $response->assertRedirect();

    expect($task->fresh()->completed_at)->not->toBeNull();
});

test('authenticated users can unmark a completed task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->completed()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.update', $task));

    $response->assertRedirect();

    expect($task->fresh()->completed_at)->toBeNull();
});

test('users cannot toggle another users task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->for($otherUser)->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.update', $task));

    $response->assertForbidden();
});

test('authenticated users can delete a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('tasks.destroy', $task));

    $response->assertRedirect();

    expect(Task::find($task->id))->toBeNull();
});

test('users cannot delete another users task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->for($otherUser)->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('tasks.destroy', $task));

    $response->assertForbidden();
});

test('tasks listing only shows user own tasks', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userTask = Task::factory()->for($user)->create(['title' => 'My task']);
    $otherTask = Task::factory()->for($otherUser)->create(['title' => 'Not mine']);

    $response = $this
        ->actingAs($user)
        ->get(route('tasks.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('tasks/Index')
        ->has('tasks.data', 1)
        ->where('tasks.data.0.title', 'My task')
    );
});

test('authenticated users can view trashed tasks with trash filter', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();
    $task->delete();

    $response = $this
        ->actingAs($user)
        ->get(route('tasks.index', ['filter' => 'trash']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('tasks/Index')
        ->has('tasks.data', 1)
        ->where('tasks.data.0.title', $task->title)
    );
});

test('trash filter does not show non-deleted tasks', function () {
    $user = User::factory()->create();
    Task::factory()->for($user)->create(['title' => 'Active task']);

    $response = $this
        ->actingAs($user)
        ->get(route('tasks.index', ['filter' => 'trash']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('tasks/Index')
        ->has('tasks.data', 0)
    );
});

test('authenticated users can restore a trashed task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();
    $task->delete();

    $this->assertSoftDeleted('tasks', ['id' => $task->id]);

    $response = $this
        ->actingAs($user)
        ->post(route('tasks.restore', $task));

    $response->assertRedirect();

    expect($task->fresh()->deleted_at)->toBeNull();
});

test('users cannot restore another users task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->for($otherUser)->create();
    $task->delete();

    $response = $this
        ->actingAs($user)
        ->post(route('tasks.restore', $task));

    $response->assertForbidden();
});

test('authenticated users can start a pending task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create([
        'status' => TaskStatus::Pending,
        'started_at' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.start', $task));

    $response->assertRedirect();

    $task->refresh();

    expect($task->status)->toBe(TaskStatus::InProgress)
        ->and($task->started_at)->not->toBeNull();
});

test('authenticated users cannot start a completed task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->completed()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.start', $task));

    $response->assertRedirect();

    $task->refresh();

    expect($task->status)->toBe(TaskStatus::Completed)
        ->and($task->started_at)->toBeNull();
});

test('users cannot start another users task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->for($otherUser)->create([
        'status' => TaskStatus::Pending,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.start', $task));

    $response->assertForbidden();
});

test('completing a task updates status to completed', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create([
        'status' => TaskStatus::InProgress,
        'started_at' => now(),
    ]);

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.update', $task));

    $response->assertRedirect();

    $task->refresh();

    expect($task->completed_at)->not->toBeNull()
        ->and($task->status)->toBe(TaskStatus::Completed);
});

test('reopening a task resets status to pending', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->completed()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.update', $task));

    $response->assertRedirect();

    $task->refresh();

    expect($task->completed_at)->toBeNull()
        ->and($task->status)->toBe(TaskStatus::Pending);
});

test('task listing shows started tasks in pending filter', function () {
    $user = User::factory()->create();

    $pendingTask = Task::factory()->for($user)->create([
        'title' => 'Pending task',
        'status' => TaskStatus::Pending,
    ]);

    $inProgressTask = Task::factory()->for($user)->inProgress()->create([
        'title' => 'In progress task',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('tasks.index', ['filter' => 'pending']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('tasks/Index')
        ->has('tasks.data', 2)
    );
});

test('new task is created with pending status', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('tasks.store'), [
            'title' => 'New task',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'user_id' => $user->id,
        'title' => 'New task',
        'status' => TaskStatus::Pending->value,
    ]);
});

test('authenticated users can update task title', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['title' => 'Old title']);

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.updateTitle', $task), [
            'title' => 'New title',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'New title',
    ]);
});

test('task title update requires a title', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['title' => 'Original title']);

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.updateTitle', $task), [
            'title' => '',
        ]);

    $response->assertSessionHasErrors('title');
});

test('task title update requires max 255 characters', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.updateTitle', $task), [
            'title' => str_repeat('a', 256),
        ]);

    $response->assertSessionHasErrors('title');
});

test('users cannot update another users task title', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->for($otherUser)->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.updateTitle', $task), [
            'title' => 'Hacked title',
        ]);

    $response->assertForbidden();
});

test('authenticated users can update task due date', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['due_date' => null]);

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.updateDueDate', $task), [
            'due_date' => '2026-07-20',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'due_date' => '2026-07-20',
    ]);
});

test('authenticated users can remove task due date', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['due_date' => '2026-06-15']);

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.updateDueDate', $task), [
            'due_date' => null,
        ]);

    $response->assertRedirect();

    expect($task->fresh()->due_date)->toBeNull();
});

test('users cannot update another users task due date', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->for($otherUser)->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.updateDueDate', $task), [
            'due_date' => '2026-12-25',
        ]);

    $response->assertForbidden();
});

test('authenticated users can update task priority', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['priority' => 'medium']);

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.updatePriority', $task), [
            'priority' => 'high',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'priority' => 'high',
    ]);
});

test('task priority update requires valid priority value', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.updatePriority', $task), [
            'priority' => 'invalid',
        ]);

    $response->assertSessionHasErrors('priority');
});

test('users cannot update another users task priority', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->for($otherUser)->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('tasks.updatePriority', $task), [
            'priority' => 'urgent',
        ]);

    $response->assertForbidden();
});
