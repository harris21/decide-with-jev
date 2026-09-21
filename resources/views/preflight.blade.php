<x-layout title="Content Preflight Checker">
    <h1>Content Preflight Checker</h1>
    <p>Course demo: use the supplied routing brief. Nothing publishes here.</p>
    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <div class="columns">
        <section>
            <form method="POST" action="/preflight/draft">
                @csrf
                <label>Brief
                    <textarea name="brief" required maxlength="1000">{{ old('brief', $draft['brief'] ?? '') }}</textarea>
                </label>
                <label>Draft
                    <textarea name="draft" required maxlength="12000">{{ old('draft', $draft['draft'] ?? '') }}</textarea>
                </label>
                <button>Save draft</button>
            </form>
        </section>
        <section>
            <p>Save changes before checking. Run check uses the saved draft only.</p>
            @if ($draft)
                <p>Saved input version: {{ $draft['version'] }}</p>
                <form method="POST" action="/preflight/check">
                    @csrf
                    <button>Run check on saved draft</button>
                </form>
            @endif
            <section class="result"
                data-recommendation="{{ $run['recommendation'] ?? 'none' }}"
                data-status="{{ $run['status'] ?? 'none' }}"
                data-current="{{ ($current ?? false) ? 'yes' : 'no' }}">
                @if ($run)
                    <h2>{{ $current ? 'Current check' : 'Stale check. Run again.' }}</h2>
                    <p>Machine recommendation: {{ $run['recommendation'] }}</p>
                    <p>Run status: {{ $run['status'] }}</p>
                    <pre>{{ json_encode($run['answers'], JSON_PRETTY_PRINT) }}</pre>
                    @if ($current)
                        <form method="POST" action="/preflight/decision">
                            @csrf
                            <input type="hidden" name="run_id" value="{{ $run['id'] }}">
                            <label>Decision
                                <select name="decision">
                                    <option value="accept-for-editing">Accept for editing</option>
                                    <option value="revise">Revise</option>
                                    <option value="needs-review">Needs review</option>
                                </select>
                            </label>
                            <label>Reason <input name="reason" required maxlength="500"></label>
                            <button>Save editor decision</button>
                        </form>
                    @endif
                    @if ($run['human'])
                        <p>Editor decision: {{ $run['human']['decision'] }}</p>
                        <p>{{ $run['human']['reason'] }}</p>
                    @endif
                @endif
            </section>
        </section>
    </div>
</x-layout>
