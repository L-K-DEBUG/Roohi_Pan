<?php

it('redirects guests to sign in', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login', absolute: false));
});
