<?php

use App\Models\Tournament;
use App\Models\User;

it('redirects guests when trying to follow or join', function () {
    $tournament = Tournament::factory()->for(User::factory(), 'organizer')->create();

    $this->post(route('tournaments.follow', $tournament))->assertRedirect(route('login'));
    $this->post(route('tournaments.join', $tournament))->assertRedirect(route('login'));
});

it('allows an authenticated user to follow and unfollow a tournament', function () {
    $user = User::factory()->create();
    $tournament = Tournament::factory()->for(User::factory(), 'organizer')->create([
        'followers_count' => 0,
    ]);

    $this->actingAs($user)
        ->post(route('tournaments.follow', $tournament))
        ->assertRedirect();

    expect($tournament->fresh()->followers_count)->toBe(1);
    expect($tournament->followers()->where('users.id', $user->id)->exists())->toBeTrue();

    $this->actingAs($user)
        ->delete(route('tournaments.unfollow', $tournament))
        ->assertRedirect();

    expect($tournament->fresh()->followers_count)->toBe(0);
    expect($tournament->followers()->where('users.id', $user->id)->exists())->toBeFalse();
});

it('allows an authenticated user to join and leave when registration is open', function () {
    $user = User::factory()->create();
    $tournament = Tournament::factory()->for(User::factory(), 'organizer')->create([
        'status' => 'registration_open',
        'registration_deadline' => now()->addDay(),
        'max_participants' => 16,
        'participants_count' => 0,
    ]);

    $this->actingAs($user)
        ->post(route('tournaments.join', $tournament))
        ->assertRedirect();

    expect($tournament->fresh()->participants_count)->toBe(1);
    expect($tournament->participants()->where('users.id', $user->id)->exists())->toBeTrue();

    $this->actingAs($user)
        ->delete(route('tournaments.leave', $tournament))
        ->assertRedirect();

    expect($tournament->fresh()->participants_count)->toBe(0);
    expect($tournament->participants()->where('users.id', $user->id)->exists())->toBeFalse();
});

it('prevents joining when registration is closed or full', function () {
    $user = User::factory()->create();

    $closedTournament = Tournament::factory()->for(User::factory(), 'organizer')->create([
        'status' => 'upcoming',
        'registration_deadline' => now()->addDay(),
        'max_participants' => 16,
        'participants_count' => 0,
    ]);

    $fullTournament = Tournament::factory()->for(User::factory(), 'organizer')->create([
        'status' => 'registration_open',
        'registration_deadline' => now()->addDay(),
        'max_participants' => 8,
        'participants_count' => 0,
    ]);

    $existingParticipants = User::factory()->count(8)->create();
    $fullTournament->participants()->sync($existingParticipants->pluck('id')->all());
    $fullTournament->update(['participants_count' => $fullTournament->participants()->count()]);

    $this->actingAs($user)->post(route('tournaments.join', $closedTournament))->assertRedirect();
    $this->actingAs($user)->post(route('tournaments.join', $fullTournament))->assertRedirect();

    expect($closedTournament->participants()->where('users.id', $user->id)->exists())->toBeFalse();
    expect($fullTournament->participants()->where('users.id', $user->id)->exists())->toBeFalse();

    expect($closedTournament->fresh()->participants_count)->toBe(0);
    expect($fullTournament->fresh()->participants_count)->toBe(8);
});
