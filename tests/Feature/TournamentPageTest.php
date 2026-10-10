<?php

use App\Models\Tournament;
use App\Models\User;

it('shows public tournaments on the index page', function () {
    $organizer = User::factory()->create();

    $publicTournament = Tournament::factory()->for($organizer, 'organizer')->create([
        'name' => 'Public Cup',
        'slug' => 'public-cup',
        'is_public' => true,
    ]);

    Tournament::factory()->for($organizer, 'organizer')->create([
        'name' => 'Hidden Cup',
        'slug' => 'hidden-cup',
        'is_public' => false,
    ]);

    $response = $this->get(route('tournaments.index'));

    $response->assertOk();
    $response->assertSee('Public Cup');
    $response->assertDontSee('Hidden Cup');
    $response->assertSee(route('tournaments.show', $publicTournament));
});

it('shows the tournament detail page for public tournaments', function () {
    $organizer = User::factory()->create();

    $tournament = Tournament::factory()->for($organizer, 'organizer')->create([
        'name' => 'Spring Clash',
        'slug' => 'spring-clash',
        'is_public' => true,
        'rules' => 'Best of three finals.',
    ]);

    $response = $this->get(route('tournaments.show', $tournament));

    $response->assertOk();
    $response->assertSee('Spring Clash');
    $response->assertSee('Best of three finals.');
});

it('returns 404 for private tournament detail pages', function () {
    $organizer = User::factory()->create();

    $privateTournament = Tournament::factory()->for($organizer, 'organizer')->create([
        'slug' => 'private-lobby',
        'is_public' => false,
    ]);

    $this->get(route('tournaments.show', $privateTournament))->assertNotFound();
});
