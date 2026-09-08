@props([
    'title' => 'Console',
    'subtitle' => null,
])

@php
    use App\Support\PublicationGates;
    use App\Models\RfpSubmission;

    $openGates = PublicationGates::openCount();
    $newRfp = RfpSubmission::query()->whereNull('acknowledged_at')->count();

    $nav = [
        'Overview' => [
            ['Dashboard', 'admin.dashboard', $openGates ?: null, true],
        ],
        'Publication gates' => [
            ['Site settings', 'admin.settings.edit', null, false],
            ['Metrics', 'admin.metrics.index', null, false],
            ['Compliance', 'admin.compliance.index', null, false],
            ['Platforms', 'admin.platforms.index', null, false],
            ['Leadership', 'admin.team.index', null, false],
            ['Vacancies', 'admin.jobs.index', null, false],
        ],
        'Content' => [
            ['Pillars', 'admin.content.index', null, false, 'pillars'],
            ['Services', 'admin.content.index', null, false, 'services'],
            ['Industries', 'admin.content.index', null, false, 'industries'],
            ['Engagement models', 'admin.content.index', null, false, 'engagement-models'],
            ['Insights', 'admin.insights.index', null, false],
        ],
        'Enquiries' => [
            ['RFP inbox', 'admin.rfp.index', $newRfp ?: null, true],
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} — {{ config('company.legal_name') }} console</title>
    {{ Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="adm">
        <nav class="adm__rail" aria-label="Console">
            <a class="adm__brand" href="{{ route('admin.dashboard') }}">
                <strong>JIRANISOKO</strong>
                <span>Console</span>
            </a>

            @foreach ($nav as $group => $items)
                <div class="adm__group">
                    <p class="adm__grouplabel">{{ $group }}</p>
                    @foreach ($items as $item)
                        @php([$label, $route, $count, $alert] = $item)
                        @php($param = $item[4] ?? null)
                        <a
                            class="adm__link"
                            href="{{ $param ? route($route, $param) : route($route) }}"
                            @if ($param ? request()->routeIs($route) && request()->route('type') === $param : request()->routeIs($route)) aria-current="page" @endif
                        >
                            <span>{{ $label }}</span>
                            @if ($count)
                                <span class="adm__count @if ($alert) adm__count--alert @endif">{{ $count }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endforeach

            <div class="adm__foot">
                <span>Signed in as {{ auth()->user()?->name }}</span>
                <a class="adm__link" href="{{ route('home') }}" style="padding-left:0">View site &rarr;</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn--ghost btn--sm" style="width:100%">Sign out</button>
                </form>
            </div>
        </nav>

        <main class="adm__main">
            <div class="adm__head">
                <div>
                    <h1 class="adm__title">{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="adm__sub">{{ $subtitle }}</p>
                    @endif
                </div>
                {{ $actions ?? '' }}
            </div>

            @if (session('status'))
                <p class="adm__flash">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <div class="adm__errors" role="alert">
                    <strong>That could not be saved</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>
</html>
