@props([
    'heading',
    'desc' => null,
    'rule' => true,
])

<div class="blockhead">
    <h2>{{ $heading }}</h2>
    @if ($rule)
        <span class="blockhead__rule" aria-hidden="true"></span>
    @endif
    @if ($desc)
        <p class="blockhead__desc">{{ $desc }}</p>
    @endif
    {{ $slot }}
</div>
