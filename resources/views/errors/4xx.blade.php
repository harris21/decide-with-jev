<x-layout title="{{ $exception->getMessage() ?: 'Request not accepted' }}" :narrow="true">
    <h1>{{ $exception->getMessage() ?: 'Request not accepted' }}</h1>
    <p>Nothing was saved from that request.</p>
    @if ($exception->getStatusCode() === 409)
        <p>The draft or its check changed since this page was opened.</p>
    @endif
    <p><a href="/preflight">Back to the preflight screen</a></p>
</x-layout>
