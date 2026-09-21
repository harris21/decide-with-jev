<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

it('replaces an old recommendation when the provider fails', function () {
    Http::preventStrayRequests();
    Http::fake(['api.typesafe.ai/*' => Http::response([], 503)]);
    config(['ai.providers.typesafe.key' => 'synthetic-test-key']);
    $samples = require resource_path('preflight/samples.php');
    $this->actingAs(User::factory()->editor()->create())
        ->withSession([
            'preflight.draft' => ['brief' => $samples['brief'], 'draft' => $samples['complete'], 'version' => 1],
            'preflight.run' => ['recommendation' => 'ready-for-editor'],
        ])->post('/preflight/check')
        ->assertRedirect('/preflight')
        ->assertSessionHas('preflight.run.status', 'failed')
        ->assertSessionHas('preflight.run.recommendation', 'needs-review');
    Http::assertSent(fn ($request) => $request->url() === 'https://api.typesafe.ai/v1/systemone');
});
