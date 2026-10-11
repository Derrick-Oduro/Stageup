<?php

use App\Models\Fixture;
use App\Models\Tournament;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'registered_user', 'guard_name' => 'web']);
});

it('shows fixtures awaiting verification to admins', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $organizer = User::factory()->create();
    $playerA = User::factory()->create();
    $playerB = User::factory()->create();

    $tournament = Tournament::factory()->for($organizer, 'organizer')->create();
    $fixture = Fixture::create([
        'tournament_id' => $tournament->id,
        'round_name' => 'Final',
        'status' => 'awaiting_verification',
        'scheduled_at' => now()->addDay(),
    ]);

    $fixture->participants()->attach([
        $playerA->id => ['side' => 'home'],
        $playerB->id => ['side' => 'away'],
    ]);

    $this->actingAs($admin)
        ->get(route('admin.fixtures'))
        ->assertOk()
        ->assertSee('Fixtures awaiting verification')
        ->assertSee('Final');
});

it('allows an admin to verify a fixture result', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $organizer = User::factory()->create();
    $playerA = User::factory()->create();
    $playerB = User::factory()->create();

    $tournament = Tournament::factory()->for($organizer, 'organizer')->create();
    $fixture = Fixture::create([
        'tournament_id' => $tournament->id,
        'round_name' => 'Final',
        'status' => 'awaiting_verification',
        'scheduled_at' => now()->addDay(),
    ]);

    $fixture->participants()->attach([
        $playerA->id => ['side' => 'home'],
        $playerB->id => ['side' => 'away'],
    ]);

    $fixture->scoreSubmissions()->createMany([
        [
            'submitted_by_user_id' => $playerA->id,
            'score_for' => 2,
            'score_against' => 1,
            'status' => 'submitted',
        ],
        [
            'submitted_by_user_id' => $playerB->id,
            'score_for' => 2,
            'score_against' => 1,
            'status' => 'submitted',
        ],
    ]);

    $this->actingAs($admin)
        ->post(route('admin.fixtures.verify', ['fixture' => $fixture]), [
            'winner_user_id' => $playerA->id,
            'loser_user_id' => $playerB->id,
            'score_for' => 2,
            'score_against' => 1,
            'reason' => 'Scores match and were verified.',
        ])
        ->assertRedirect(route('admin.fixtures'));

    expect($fixture->fresh()->status)->toBe('verified')
        ->and($fixture->officialResult)->not->toBeNull()
        ->and($fixture->officialResult->winner_user_id)->toBe($playerA->id)
        ->and($fixture->officialResult->loser_user_id)->toBe($playerB->id);
});
