<?php

use App\Models\CommunityMessage;
use App\Models\Tournament;
use App\Models\User;

it('renders the public community tab pages', function () {
    $tournament = Tournament::factory()->create([
        'is_public' => true,
    ]);

    $this->get(route('fixtures.index'))
        ->assertOk()
        ->assertSee('Fixtures');

    $this->get(route('table.index', ['tournament' => $tournament->slug]))
        ->assertOk()
        ->assertSee('League table')
        ->assertSee($tournament->name);

    $this->get(route('community.index'))
        ->assertOk()
        ->assertSee('Community')
        ->assertSee('Community chat');
});

it('allows authenticated members to post community messages', function () {
    $user = User::factory()->create(['name' => 'Street Player']);

    $this->actingAs($user)
        ->post(route('community.messages.store'), [
            'body' => 'Ready for matchday.',
        ])
        ->assertRedirect(route('community.index').'#chat');

    expect(CommunityMessage::query()->where('body', 'Ready for matchday.')->first())
        ->user_id->toBe($user->id);

    $this->get(route('community.index'))
        ->assertOk()
        ->assertSee('Ready for matchday.')
        ->assertSee('Street Player');
});

it('requires authentication to post community messages', function () {
    $this->post(route('community.messages.store'), ['body' => 'No access'])
        ->assertRedirect(route('login'));
});