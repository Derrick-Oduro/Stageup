<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'registered_user', 'guard_name' => 'web']);
});

it('blocks non-admin users from the admin dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('registered_user');

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('allows admin users to access the admin dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Admin dashboard');
});
