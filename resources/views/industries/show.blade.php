{{-- Template T-07 — industry page. --}}

<x-layouts.app :title="$industry->title" :description="$industry->constraint_statement">
    <x-site.page-header
        eyebrow="Sector"
        :heading="$industry->title"
        :standfirst="$industry->constraint_statement"
        :crumbs="['Industries' => route('industries.index'), $industry->title => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell stack-lg">
            <div>
                <h2 class="eyebrow" style="margin-bottom:1rem">Pressures we design against</h2>
                <div class="deflist">
                    @foreach ($industry->pressures as $index => $pressure)
                        <div class="deflist__row">
                            <dt>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</dt>
                            <dd>{{ $pressure }}</dd>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($industry->regulatory_notes)
                <div class="note">
                    <span class="note__tag">Regulatory posture</span>
                    <p>{{ $industry->regulatory_notes }}</p>
                </div>
            @endif
        </div>
    </section>

    <section class="section section--tinted">
        <div class="shell">
            <div class="section-head">
                <p class="eyebrow">Capability mapping</p>
                <h2 class="display-3">The disciplines this sector draws on</h2>
            </div>
            <div class="hairline-grid hairline-grid--3">
                @foreach ($pillars as $pillar)
                    <a class="cell" href="{{ route('pillars.show', $pillar) }}">
                        <span class="cell__marker">Pillar {{ $pillar->reference() }}</span>
                        <h3 class="cell__title" style="font-size:1.02rem">{{ $pillar->nav_title }}</h3>
                        <p class="cell__body" style="font-size:.86rem">{{ $pillar->descriptor }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-site.rfp-band />
</x-layouts.app>
