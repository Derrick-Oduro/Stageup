<?php

use App\Models\Fixture;
use App\Models\Tournament;
use App\Models\User;
use Spatie\Permission\Models\Role;

it('can compare matching score submissions and approve a verified result', function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $organizer = User::factory()->create();
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $playerA = User::factory()->create();
    $playerB = User::factory()->create();

    $tournament = Tournament::factory()->for($organizer, 'organizer')->create([
        'status' => 'registration_open',
        'max_participants' => 2,
    ]);

    $fixture = Fixture::create([
        'tournament_id' => $tournament->id,
        'round_name' => 'Final',
        'status' => 'awaiting_scores',
        'scheduled_at' => now()->addDay(),
        'reporting_deadline' => now()->addHours(24),
    ]);

    $fixture->participants()->attach([
        $playerA->id => ['side' => 'home'],
        $playerB->id => ['side' => 'away'],
    ]);

    $fixture->submitScore($playerA, 2, 1);
    $fixture->submitScore($playerB, 2, 1);

    expect($fixture->fresh()->status)->toBe('awaiting_verification')
        ->and($fixture->scoreSubmissions()->count())->toBe(2);

    $fixture->approveResult($admin, $playerA, $playerB, 2, 1, 'Scores match and were verified.');

    expect($fixture->fresh()->status)->toBe('verified')
        ->and($fixture->officialResult)->not->toBeNull()
        ->and($fixture->officialResult->winner_user_id)->toBe($playerA->id)
        ->and($fixture->officialResult->loser_user_id)->toBe($playerB->id);
});
