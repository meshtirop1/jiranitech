{{-- Template T-02 — service taxonomy index. --}}

<x-layouts.app
    title="Services"
    description="The complete Jiranisoko Tech Solutions capability taxonomy — six engineering disciplines and twenty-two specialist service lines across AI, cloud, enterprise software, payments, core frameworks and distributed ledger."
>
    <x-site.page-header
        eyebrow="Capability taxonomy"
        heading="Six engineering disciplines. Twenty-two service lines."
        standfirst="Enterprise programmes rarely sit inside a single discipline. This is the complete taxonomy, published in full so that a technical evaluator can assess depth without a sales conversation."
        :crumbs="['Services' => null]"
    />

    <section class="section">
        <div class="shell stack-lg">
            @foreach ($pillars as $pillar)
                <article class="stack" id="{{ $pillar->slug }}">
                    <p class="eyebrow">Pillar {{ $pillar->reference() }} &middot; /services/{{ $pillar->slug }}</p>
                    <h2 class="display-3">
                        <a href="{{ route('pillars.show', $pillar) }}" style="text-decoration:none;color:inherit">{{ $pillar->title }}</a>
                    </h2>
                    <p class="prose-body measure">{{ $pillar->thesis }}</p>

                    <div class="hairline-grid hairline-grid--3" style="margin-top:.5rem">
                        @foreach ($pillar->services as $service)
                            <a class="cell" href="{{ route('services.show', [$pillar, $service]) }}">
                                <h3 class="cell__title" style="font-size:1rem">{{ $service->title }}</h3>
                                <p class="cell__body" style="font-size:.86rem">{{ Str::limit($service->executive_summary, 150) }}</p>
                                <span class="cell__foot">{{ $service->sla_tier->label() }} tier &rarr;</span>
                            </a>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <x-site.rfp-band heading="Tell us which of these you need, and we will tell you what it takes." />
</x-layouts.app>
