<?php

use App\Models\User;

it('shows the sign in page to guests', function () {
    $this->get('/login')->assertOk()->assertSee('Sign in');
});

it('sends guests to the sign in page', function () {
    $this->get('/preflight')->assertRedirect('/login');
});

it('signs in with valid credentials', function () {
    $user = User::factory()->editor()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect('/preflight');

    $this->assertAuthenticatedAs($user);
});

it('rejects a wrong password', function () {
    $user = User::factory()->editor()->create();

    $this->from('/login')
        ->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
        ->assertRedirect('/login')
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('signs out', function () {
    $this->actingAs(User::factory()->editor()->create())
        ->post('/logout')
        ->assertRedirect('/login');

    $this->assertGuest();
});
