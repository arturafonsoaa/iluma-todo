<?php

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
