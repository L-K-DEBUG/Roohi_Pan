<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'username' => 'test-user',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('pos.index', absolute: false));
    $this->assertDatabaseHas('users', ['username' => 'test-user', 'role' => 'owner']);
});

test('public registration cannot grant owner privileges after the first account', function () {
    \App\Models\User::factory()->create(['role' => 'owner']);

    $this->post('/register', [
        'username' => 'new-employee',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'owner',
    ])->assertRedirect(route('pos.index', absolute: false));

    $this->assertDatabaseHas('users', ['username' => 'new-employee', 'role' => 'employee']);
});
