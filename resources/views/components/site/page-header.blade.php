@props([
    'eyebrow' => null,
    'heading',
    'standfirst' => null,
    'crumbs' => [],
])

<section class="section" style="padding-bottom:0">
    <div class="shell">
        @if (! empty($crumbs))
            <nav aria-label="Breadcrumb">
                <ol class="crumbs">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    @foreach ($crumbs as $label => $url)
                        <li>
                            @if ($url)
                                <a href="{{ $url }}">{{ $label }}</a>
                            @else
                                <span>{{ $label }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>

            {{-- The machine-readable copy of the trail above. Emitted here rather than
                 from each page so the two can never disagree: there is one crumb list,
                 and both the reader's version and the crawler's are rendered from it. --}}
            <script type="application/ld+json">{!! \App\Support\StructuredData::graph([
                \App\Support\StructuredData::breadcrumbs($crumbs),
            ]) !!}</script>
        @endif

        <div class="section-head" style="margin-bottom:0">
            @if ($eyebrow)
                <p class="eyebrow">{{ $eyebrow }}</p>
            @endif
            <h1 class="display-2">{{ $heading }}</h1>
            @if ($standfirst)
                <p class="lede section-head__intro">{{ $standfirst }}</p>
            @endif
            {{ $slot ?? '' }}
        </div>
    </div>
</section>
