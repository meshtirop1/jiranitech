{{--
    Dark overlay hero with a centred title, subtitle and a single call to action,
    followed by a breadcrumb strip on a light ground — the devcom.com page pattern.

    The ground is a pine gradient over a fine grid rather than a photograph. The
    brand guidance in JTS-WEB-IA-001 prohibits stock imagery of people at laptops,
    and no commissioned photography exists yet. Swap the ::before layer in app.css
    for a background-image once real photography is available; nothing else changes.
--}}

@props([
    'eyebrow' => null,
    'heading',
    'subtitle' => null,
    'ctaLabel' => null,
    'ctaUrl' => null,
    'crumbs' => [],
])

<section class="pagehero">
    <div class="shell pagehero__inner">
        @if ($eyebrow)
            <p class="pagehero__eyebrow">{{ $eyebrow }}</p>
        @endif

        <h1>{{ $heading }}</h1>

        @if ($subtitle)
            <p class="pagehero__sub">{{ $subtitle }}</p>
        @endif

        @if ($ctaLabel && $ctaUrl)
            <a class="btn btn--on-pine" href="{{ $ctaUrl }}" style="margin-top:.5rem">{{ $ctaLabel }}</a>
        @endif
    </div>
</section>

@if (! empty($crumbs))
    <div class="crumbbar">
        <div class="shell">
            <nav aria-label="Breadcrumb">
                <ol class="crumbs">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    @foreach ($crumbs as $label => $url)
                        <li>
                            @if ($url)
                                <a href="{{ $url }}">{{ $label }}</a>
                            @else
                                <span aria-current="page">{{ $label }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>
    </div>
@endif
