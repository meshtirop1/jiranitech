{{--
    Template T-04 — the modular core service page, instantiated twenty-two times.

    Required sections per JTS-WEB-IA-001 §2.4: executive summary, outcomes and
    capabilities, architecture and stack, SLA/compliance/governance, engagement routes,
    conversion band. Every instance renders the same field set from the Service model,
    which is what makes twenty-two pages maintainable and comparable to each other.
--}}

<x-layouts.app
    :title="$service->title"
    :description="$service->meta_description ?? Str::limit($service->executive_summary, 300)"
>
    <x-site.page-header
        :eyebrow="'Pillar '.$pillar->reference().' — '.$pillar->nav_title"
        :heading="$service->title"
        :crumbs="[
            'Services' => route('services.index'),
            $pillar->nav_title => route('pillars.show', $pillar),
            $service->title => null,
        ]"
    />

    {{-- Executive summary --}}
    <section class="section" style="padding-top:2.5rem">
        <div class="shell">
            <p class="lede measure" style="font-size:1.15rem">{{ $service->executive_summary }}</p>

            <div class="btn-row" style="margin-top:1.75rem">
                <a class="btn btn--primary" href="{{ route('rfp.create', ['track' => \App\Enums\RfpTrack::NewProductBuild->value]) }}">Request a proposal for this capability</a>
                <a class="btn btn--secondary" href="{{ route('contact.engagement-desk') }}">Speak with the practice lead</a>
            </div>
        </div>
    </section>

    {{-- Outcomes and capabilities --}}
    <section class="section section--tinted">
        <div class="shell">
            <div class="hairline-grid hairline-grid--2">
                <div class="cell">
                    <span class="cell__marker">Business outcomes</span>
                    <h2 class="cell__title">What you get from the engagement</h2>
                    <ul class="cell__list" style="margin-top:.6rem">
                        @foreach ($service->outcomes as $outcome)
                            <li style="font-size:.9rem;color:var(--ink-2)">{{ $outcome }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="cell">
                    <span class="cell__marker">Technical capabilities</span>
                    <h2 class="cell__title">What we do inside it</h2>
                    <ul class="cell__list" style="margin-top:.6rem">
                        @foreach ($service->capabilities as $capability)
                            <li style="font-size:.9rem;color:var(--ink-2)">{{ $capability }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Architecture and stack --}}
    <section class="section">
        <div class="shell stack-lg">
            <div class="section-head" style="margin-bottom:0">
                <p class="eyebrow">Architecture and stack</p>
                <h2 class="display-3">How we build it</h2>
            </div>

            @if ($service->architecture_note)
                <div class="note">
                    <span class="note__tag">Architectural position</span>
                    <p>{{ $service->architecture_note }}</p>
                </div>
            @endif

            <div>
                <h3 class="eyebrow" style="margin-bottom:.75rem">Representative technologies</h3>
                <ul class="taglist">
                    @foreach ($service->stack as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
                <p class="small muted" style="margin-top:.9rem;max-width:60ch">
                    Technology selection follows the requirement. This list is representative of engagements in
                    this service line, not a constraint we impose on yours.
                </p>
            </div>
        </div>
    </section>

    {{-- SLA, compliance and governance --}}
    <section class="section section--tinted">
        <div class="shell stack-lg">
            <div class="section-head" style="margin-bottom:0">
                <p class="eyebrow">Service assurance</p>
                <h2 class="display-3">SLA, compliance and governance</h2>
                <p class="lede section-head__intro">
                    Engagements in this service line are delivered under the
                    <strong>{{ $service->sla_tier->label() }}</strong> service tier by default, against the
                    compliance obligations below. Both are set in the engagement contract, not by this page.
                </p>
            </div>

            <div class="deflist">
                <div class="deflist__row">
                    <dt>Default tier</dt>
                    <dd>{{ $service->sla_tier->label() }} — {{ $service->sla_tier->availabilityTarget() }} availability target, P1 response within {{ $service->sla_tier->priorityOneResponse() }}, coverage {{ $service->sla_tier->coverage() }}.</dd>
                </div>
                <div class="deflist__row">
                    <dt>Compliance scope</dt>
                    <dd>
                        <ul class="taglist">
                            @foreach ($service->compliance_tags as $tag)
                                <li>{{ $tag }}</li>
                            @endforeach
                        </ul>
                    </dd>
                </div>
                <div class="deflist__row">
                    <dt>Governance</dt>
                    <dd>Written architecture decision records, weekly delivery reporting, and a documented handover comprising runbooks, source and credential transfer at engagement close.</dd>
                </div>
                <div class="deflist__row">
                    <dt>Intellectual property</dt>
                    <dd>Client-commissioned work product vests in the client on payment. Source escrow available on request.</dd>
                </div>
            </div>

            <x-site.sla-table />
        </div>
    </section>

    {{-- Related service lines --}}
    @if ($related->isNotEmpty())
        <section class="section">
            <div class="shell">
                <div class="section-head">
                    <p class="eyebrow">Also in {{ $pillar->nav_title }}</p>
                    <h2 class="display-3">Related service lines</h2>
                </div>
                <div class="hairline-grid hairline-grid--3">
                    @foreach ($related as $sibling)
                        <a class="cell" href="{{ route('services.show', [$pillar, $sibling]) }}">
                            <h3 class="cell__title" style="font-size:1rem">{{ $sibling->title }}</h3>
                            <p class="cell__body" style="font-size:.86rem">{{ Str::limit($sibling->executive_summary, 130) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.rfp-band />
</x-layouts.app>
