{{--
    Link to the holding company, Jiranisoko Market Ltd.

    It resolves to the holding company's own corporate site when one is configured,
    and otherwise to this site's group section. It must never fall back to the
    marketplace: someone who clicks "Investor & Group" is looking for corporate
    information, and landing them on a consumer classifieds app reads as a firm that
    cannot tell its own entities apart. Use <x-site.marketplace-link> for that.
--}}

@props([
    'label' => null,
])

@php
    $parentUrl = config('company.parent.url');
    $isExternal = filled($parentUrl);
    $href = $isExternal ? $parentUrl : route('company.about').'#group';
@endphp

<a
    href="{{ $href }}"
    @if ($isExternal) rel="noopener" target="_blank" @endif
    {{ $attributes }}
>{{ $label ?? config('company.parent.name') }}</a>
