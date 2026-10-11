<?php

use App\Models\Tournament;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'registered_user', 'guard_name' => 'web']);
});

it('shows the admin tournament management page for admins', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $tournament = Tournament::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.tournaments'))
        ->assertOk()
        ->assertSee('Tournament management')
        ->assertSee($tournament->name);
});

it('allows an admin to transition a tournament to a valid next status', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $tournament = Tournament::factory()->create([
        'status' => 'registration_open',
    ]);

    $this->actingAs($user)
        ->post(route('admin.tournaments.transition', ['tournament' => $tournament, 'status' => 'registration_closed']))
        ->assertRedirect(route('admin.tournaments'));

    $tournament->refresh();
    expect($tournament->status)->toBe('registration_closed');
});
