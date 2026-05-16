<?php

use App\Enums\ProjectColor;
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
            'color' => ProjectColor::Emerald->value,
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'user_id' => $user->id,
        'name' => 'Projeto principal',
        'color' => ProjectColor::Emerald->value,
    ]);

    expect(Project::query()->where('name', 'Projeto principal')->exists())->toBeTrue();
});

test('project creation requires a valid color', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('projects.store'), [
            'name' => 'Projeto inválido',
            'color' => 'invalid-color',
        ]);

    $response->assertSessionHasErrors('color');
});

test('project creation requires color', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('projects.store'), [
            'name' => 'Projeto sem cor',
        ]);

    $response->assertSessionHasErrors('color');
});
