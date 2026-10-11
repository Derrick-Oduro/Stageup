<?php

use App\Models\AuditLog;
use App\Models\Tournament;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'registered_user', 'guard_name' => 'web']);
});

it('records a status transition audit trail for admin actions', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $tournament = Tournament::factory()->create([
        'status' => 'registration_open',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.tournaments.transition', [
            'tournament' => $tournament,
            'status' => 'registration_closed',
        ]))
        ->assertRedirect(route('admin.tournaments'));

    $auditLog = AuditLog::query()
        ->where('subject_type', Tournament::class)
        ->where('subject_id', $tournament->id)
        ->where('action', 'status_changed')
        ->first();

    expect($auditLog)->not->toBeNull()
        ->and($auditLog->actor_user_id)->toBe($admin->id)
        ->and($auditLog->metadata)->toMatchArray([
            'from' => 'registration_open',
            'to' => 'registration_closed',
        ]);
});
