@props([
    'title' => null,
    'description' => null,
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' — '.config('company.legal_name') : config('company.legal_name').' — Enterprise Technology' }}</title>
    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif

    <meta property="og:site_name" content="{{ config('company.legal_name') }}">
    <meta property="og:title" content="{{ $title ?? config('company.legal_name') }}">
    @if ($description)
        <meta property="og:description" content="{{ $description }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Self-hosted PT Sans. Vite::fonts() emits the preload links and the @font-face
         block from the fonts manifest; without it the build produces the woff2 files
         but nothing references them, and the page silently falls back to Segoe UI. --}}
    {{ Vite::fonts() }}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#main">Skip to main content</a>

    <x-site.header />

    <main id="main">
        {{ $slot }}
    </main>

    <x-site.footer />
</body>
</html>
