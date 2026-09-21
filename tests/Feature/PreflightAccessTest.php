<?php

use App\Models\User;

it('lets an editor open the preflight screen', function () {
    $this->actingAs(User::factory()->editor()->create())
        ->get('/preflight')
        ->assertOk()
        ->assertSee('Content Preflight Checker')
        ->assertSee('Save draft');
});

it('forbids a signed in user who is not an editor', function () {
    $this->actingAs(User::factory()->create())
        ->get('/preflight')
        ->assertForbidden()
        ->assertSee('Log out');
});

it('does not let the editor flag be mass assigned', function () {
    $user = User::factory()->create();

    $user->fill(['is_editor' => true]);

    expect($user->is_editor)->toBeFalse();
});
