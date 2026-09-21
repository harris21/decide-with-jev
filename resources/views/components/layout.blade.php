@props(['title' => 'Content Preflight Checker', 'narrow' => false])
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @auth
        <header>
            <ul class="strip" aria-label="What each colour means">
                <li>Ready for editor</li>
                <li>Revise</li>
                <li>Needs review</li>
                <li>Stale check</li>
            </ul>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <span>{{ auth()->user()->email }}</span>
                <button>Log out</button>
            </form>
        </header>
    @endauth
    <main @class(['narrow' => $narrow])>
        <article>
            {{ $slot }}
        </article>
    </main>
</body>
</html>
