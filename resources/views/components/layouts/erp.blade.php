@props(['title' => null])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $title ? $title.' — '.config('erp.name') : config('erp.name') }}</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">

    {{ Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="erp">
    <a class="skip-link" href="#main">Skip to main content</a>

    <header class="erp__bar">
        <div class="erp__barinner">
            <a class="erp__mark" href="{{ route('erp.dashboard') }}">
                <svg viewBox="0 0 64 64" aria-hidden="true" focusable="false">
                    <path d="M41 16 V34 A9 9 0 0 1 23 34" fill="none" stroke="currentColor" stroke-width="10"/>
                </svg>
                <span><strong>Jiranisoko</strong> Delivery</span>
            </a>

            @auth
                <nav class="erp__nav" aria-label="Delivery">
                    <a href="{{ route('erp.dashboard') }}" @class(['erp__navlink', 'is-current' => request()->routeIs('erp.dashboard')])>My work</a>
                    <a href="{{ route('erp.projects.index') }}" @class(['erp__navlink', 'is-current' => request()->routeIs('erp.projects.*')])>Projects</a>

                    @php($queue = auth()->user()->reviewQueue()->count())
                    <a href="{{ route('erp.reviews.index') }}" @class(['erp__navlink', 'is-current' => request()->routeIs('erp.reviews.*')])>
                        Reviews @if ($queue > 0)<span class="erp__count">{{ $queue }}</span>@endif
                    </a>

                    @if (auth()->user()->erpRole()?->administersDelivery())
                        <a href="{{ route('erp.people.index') }}" @class(['erp__navlink', 'is-current' => request()->routeIs('erp.people.*')])>People</a>
                    @endif
                </nav>

                <div class="erp__who">
                    <span>{{ auth()->user()->name }}<em>{{ auth()->user()->erpRole()?->label() }}</em></span>
                    <form method="POST" action="{{ route('erp.logout') }}">
                        @csrf
                        <button type="submit" class="erp__signout">Sign out</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <main id="main" class="erp__main">
        <div class="erp__shell">
            @if (session('status'))
                <p class="erp__flash">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <div class="erp__errors">
                    <strong>That did not go through.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</body>
</html>
