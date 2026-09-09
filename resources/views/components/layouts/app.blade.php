@props([
    'title' => null,
    'description' => null,
    // Pages that must not enter an index: signed confirmation URLs, anything
    // addressed to one recipient. Everything else is indexable by default.
    'noindex' => false,
    // schema.org nodes for this page, merged into the site-wide graph below.
    'schema' => [],
])

@php
    // Composed rather than concatenated: the brand suffix is dropped when it
    // would push the title past what a search result renders. See Support\Seo.
    $documentTitle = \App\Support\Seo::title($title);
    $metaDescription = \App\Support\Seo::description($description);

    // Canonicals are pinned to the configured site URL, not to the request.
    // url()->current() echoes back whatever host and scheme the visitor arrived
    // on, so a crawler reaching the site over http, on the bare server IP, or on
    // a hostname we have not finished retiring would be told that URL is the
    // canonical one, and the ranking signals would split across all of them.
    $canonical = \App\Support\StructuredData::canonical(request()->path());

    $socialImage = \App\Support\StructuredData::socialImage();

    $graph = \App\Support\StructuredData::graph(array_merge([
        \App\Support\StructuredData::organisation(),
        \App\Support\StructuredData::website(),
    ], $schema));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $documentTitle }}</title>
    @if ($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif

    <meta name="robots" content="{{ $noindex ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1' }}">

    <meta property="og:site_name" content="{{ config('company.legal_name') }}">
    <meta property="og:title" content="{{ $title ?? config('company.legal_name') }}">
    @if ($description)
        <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_KE">
    <meta property="og:url" content="{{ $canonical }}">
    @if ($socialImage)
        <meta property="og:image" content="{{ $socialImage }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ config('company.legal_name') }}">
    @endif

    <meta name="twitter:card" content="{{ $socialImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $title ?? config('company.legal_name') }}">
    @if ($description)
        <meta name="twitter:description" content="{{ $metaDescription }}">
    @endif
    @if ($socialImage)
        <meta name="twitter:image" content="{{ $socialImage }}">
    @endif

    <link rel="canonical" href="{{ $canonical }}">

    {{-- Ownership tokens for the webmaster consoles. Neither sets a cookie nor
         reports a visitor, which is what keeps the privacy notice's statement
         that this site runs no analytics true. --}}
    @if (config('company.verification.google'))
        <meta name="google-site-verification" content="{{ config('company.verification.google') }}">
    @endif
    @if (config('company.verification.bing'))
        <meta name="msvalidate.01" content="{{ config('company.verification.bing') }}">
    @endif

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <script type="application/ld+json">{!! $graph !!}</script>

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
