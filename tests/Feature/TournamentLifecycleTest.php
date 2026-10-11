<?php

use App\Models\Tournament;
use App\Models\User;

it('enforces valid tournament lifecycle transitions', function () {
    $organizer = User::factory()->create();
    $tournament = Tournament::factory()->for($organizer, 'organizer')->create([
        'status' => 'upcoming',
    ]);

    expect($tournament->canTransitionTo('registration_open'))->toBeTrue()
        ->and($tournament->canTransitionTo('cancelled'))->toBeTrue()
        ->and($tournament->canTransitionTo('completed'))->toBeFalse();

    $tournament->transitionTo('registration_open');

    expect($tournament->fresh()->status)->toBe('registration_open')
        ->and($tournament->canTransitionTo('scheduled'))->toBeFalse();

    $tournament->transitionTo('cancelled');

    expect($tournament->fresh()->status)->toBe('cancelled');
});

it('stores tournament format config for the selected format', function () {
    $organizer = User::factory()->create();
    $tournament = Tournament::factory()->for($organizer, 'organizer')->create([
        'format' => 'round_robin',
        'format_config' => [
            'rounds' => 2,
            'points_for_win' => 3,
            'points_for_draw' => 1,
            'points_for_loss' => 0,
        ],
    ]);

    expect($tournament->format_config)->toMatchArray([
        'rounds' => 2,
        'points_for_win' => 3,
        'points_for_draw' => 1,
        'points_for_loss' => 0,
    ]);
});
