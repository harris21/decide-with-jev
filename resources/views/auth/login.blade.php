<x-layout title="Sign in" :narrow="true">
    <h1>Sign in</h1>
    <p>Course demo accounts: editor@example.com can run checks, writer@example.com cannot. The password for both is password.</p>
    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        </label>
        <label>Password
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <label class="inline">
            <input type="checkbox" name="remember" value="1"> Keep me signed in
        </label>
        <button>Sign in</button>
    </form>
</x-layout>
