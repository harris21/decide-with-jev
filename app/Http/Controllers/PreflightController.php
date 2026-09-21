<?php

namespace App\Http\Controllers;

use App\Services\CheckContent;
use App\Services\PreflightPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class PreflightController extends Controller
{
    public function show(Request $request): View
    {
        Gate::authorize('edit-preflight');
        $draft = $request->session()->get('preflight.draft');
        $run = $request->session()->get('preflight.run');
        $current = $this->isCurrent($draft, $run);

        return view('preflight', compact('draft', 'run', 'current'));
    }

    public function save(Request $request): RedirectResponse
    {
        Gate::authorize('edit-preflight');
        $data = $request->validate(['brief' => ['required', 'string'], 'draft' => ['required', 'string']]);
        $old = $request->session()->get('preflight.draft');
        $request->session()->put('preflight.draft', [
            ...$data, 'version' => ($old['version'] ?? 0) + 1,
        ]);

        return redirect('/preflight');
    }

    public function check(Request $request, CheckContent $checker, PreflightPolicy $policy): RedirectResponse
    {
        Gate::authorize('edit-preflight');
        $draft = $request->session()->get('preflight.draft');
        abort_unless($draft, 422, 'Save a draft first.');
        $response = $checker->check($draft['brief'], $draft['draft']);
        $request->session()->put('preflight.run', [
            'id' => (string) Str::uuid(),
            'input_version' => $draft['version'],
            'input_hash' => $this->fingerprint($draft),
            'input' => $draft,
            'requested_model' => CheckContent::MODEL,
            'returned_model' => $response->meta->model,
            'provider' => $response->meta->provider,
            'rubric' => CheckContent::RUBRIC,
            'policy' => PreflightPolicy::VERSION,
            'answers' => array_map(fn ($answer) => $answer->toArray(), $response->answers),
            'recommendation' => $policy->recommend($response),
            'status' => 'ok', 'checked_at' => now()->toIso8601String(),
            'human' => null,
        ]);

        return redirect('/preflight');
    }

    public function decide(Request $request): RedirectResponse
    {
        Gate::authorize('edit-preflight');
        $data = $request->validate([
            'run_id' => ['required', 'uuid'],
            'decision' => ['required', 'in:accept-for-editing,revise,needs-review'],
            'reason' => ['required', 'string', 'max:500'],
        ]);
        $draft = $request->session()->get('preflight.draft');
        $run = $request->session()->get('preflight.run');
        abort_unless($this->isCurrent($draft, $run) && $run['id'] === $data['run_id'], 409, 'Run a fresh check.');
        $run['human'] = [
            'decision' => $data['decision'], 'reason' => $data['reason'],
            'by' => (string) $request->user()->getAuthIdentifier(),
            'at' => now()->toIso8601String(),
        ];
        $request->session()->put('preflight.run', $run);

        return redirect('/preflight');
    }

    private function isCurrent(?array $draft, ?array $run): bool
    {
        return $draft && $run
            && $run['input_version'] === $draft['version']
            && $run['input_hash'] === $this->fingerprint($draft)
            && $run['rubric'] === CheckContent::RUBRIC
            && $run['policy'] === PreflightPolicy::VERSION
            && $run['requested_model'] === CheckContent::MODEL;
    }

    private function fingerprint(array $draft): string
    {
        return hash('sha256', json_encode([$draft['brief'], $draft['draft']], JSON_THROW_ON_ERROR));
    }
}
