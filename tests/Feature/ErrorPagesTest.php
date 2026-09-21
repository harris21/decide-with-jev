<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

it('renders client errors without a view of their own inside the app layout', function () {
    Route::middleware('web')->get('/_conflict', fn () => abort(409, 'Run a fresh check.'));

    $this->actingAs(User::factory()->editor()->create())
        ->get('/_conflict')
        ->assertStatus(409)
        ->assertSee('Run a fresh check.')
        ->assertSee('The draft or its check changed since this page was opened.')
        ->assertSee('Back to the preflight screen')
        ->assertSee('Log out');
});

it('does not blame a changed draft for other client errors', function () {
    Route::middleware('web')->get('/_unprocessable', fn () => abort(422, 'Save a draft first.'));

    $this->actingAs(User::factory()->editor()->create())
        ->get('/_unprocessable')
        ->assertStatus(422)
        ->assertSee('Save a draft first.')
        ->assertSee('Nothing was saved from that request.')
        ->assertDontSee('The draft or its check changed since this page was opened.');
});
