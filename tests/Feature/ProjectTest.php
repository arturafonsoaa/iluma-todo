<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('projects listing is no longer available', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/projects');

    $response->assertStatus(405);
});

test('authenticated users can create a project', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('projects.store'), [
            'name' => 'Projeto principal',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'user_id' => $user->id,
        'name' => 'Projeto principal',
    ]);

    expect(Project::query()->where('name', 'Projeto principal')->exists())->toBeTrue();
});
