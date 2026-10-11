<?php

it('loads the Game Street community home page', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Game Street')
        ->assertSee('Community');
});
