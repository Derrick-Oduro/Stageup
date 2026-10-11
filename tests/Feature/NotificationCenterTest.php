<?php

use App\Models\User;
use App\Notifications\TournamentUpdateNotification;

it('shows the notification center for the authenticated user', function () {
    $user = User::factory()->create();
    $user->notify(new TournamentUpdateNotification('Registration open', 'Your tournament has opened for registration.', 1));

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Notification center')
        ->assertSee('Registration open');
});

it('marks a notification as read for the authenticated user', function () {
    $user = User::factory()->create();
    $notification = $user->notify(new TournamentUpdateNotification('Reminder', 'Your fixture is due soon.', 2));

    $this->actingAs($user)
        ->post(route('notifications.read', ['id' => $user->notifications()->first()->id]))
        ->assertRedirect(route('notifications.index'));

    expect($user->fresh()->notifications()->where('id', $user->notifications()->first()->id)->first()->read_at)->not->toBeNull();
});
