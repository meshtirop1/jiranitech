{{-- Template T-03 — pillar hub. --}}

<x-layouts.app :title="$pillar->title" :description="$pillar->thesis">
    <x-site.page-header
        :eyebrow="'Pillar '.$pillar->reference()"
        :heading="$pillar->title"
        :standfirst="$pillar->thesis"
        :crumbs="['Services' => route('services.index'), $pillar->nav_title => null]"
    />

    <section class="section">
        <div class="shell">
            <h2 class="eyebrow" style="margin-bottom:1rem">Service lines in this discipline</h2>

            <div class="hairline-grid hairline-grid--2">
                @foreach ($pillar->services as $service)
                    <a class="cell" href="{{ route('services.show', [$pillar, $service]) }}">
                        <span class="cell__marker">{{ $service->sla_tier->label() }} service tier</span>
                        <h3 class="cell__title">{{ $service->title }}</h3>
                        <p class="cell__body">{{ $service->executive_summary }}</p>
                        <ul class="taglist" style="margin-top:.75rem">
                            @foreach (array_slice($service->stack, 0, 5) as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                        <span class="cell__foot">Capability detail &rarr;</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section section--tinted">
        <div class="shell">
            <div class="section-head">
                <p class="eyebrow">Adjacent disciplines</p>
                <h2 class="display-3">Most programmes draw on more than one.</h2>
            </div>
            <div class="hairline-grid hairline-grid--3">
                @foreach ($siblings as $sibling)
                    <a class="cell" href="{{ route('pillars.show', $sibling) }}">
                        <span class="cell__marker">Pillar {{ $sibling->reference() }}</span>
                        <h3 class="cell__title" style="font-size:1.02rem">{{ $sibling->nav_title }}</h3>
                        <p class="cell__body" style="font-size:.86rem">{{ $sibling->descriptor }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-site.rfp-band />
</x-layouts.app>
