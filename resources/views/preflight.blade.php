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
                {{-- Episode 4 result panel goes here. --}}
            </section>
        </section>
    </div>
</x-layout>
