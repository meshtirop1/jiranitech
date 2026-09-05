{{--
    Template T-01 — corporate homepage, sections H-02 to H-11 per JTS-WEB-IA-001 §3.

    Narrative spine: we are a serious institution (H-02) -> we operate systems rather
    than only build them (H-03, H-04) -> here is the full depth (H-05) -> here is how
    you contract us (H-06) -> here is why our location is an advantage (H-07) -> here
    is the governance that de-risks it (H-08). Everything after H-08 is routing.
--}}

<x-layouts.app
    :description="'Jiranisoko Tech Solutions architects, engineers and operates artificial intelligence, cloud infrastructure and transaction-grade software for financial institutions, public-sector agencies and high-growth enterprises. Eldoret, Kenya.'"
>

    {{-- ============================================ H-02 — hero ============ --}}
    <section class="hero" aria-labelledby="hero-heading">
        <div class="shell">
            <div class="hero__grid">
                <div class="hero__copy">
                    <p class="eyebrow">Enterprise technology division — {{ config('company.parent_name') }}</p>
                    <h1 class="display-1" id="hero-heading">We build the platforms institutions run on.</h1>
                    <p class="hero__sub">
                        {{ config('company.legal_name') }} architects, engineers and operates artificial
                        intelligence, cloud infrastructure and transaction-grade software for financial
                        institutions, public-sector agencies and high-growth enterprises. Our standards are
                        set by the systems our own group depends on every day.
                    </p>
                    <div class="btn-row">
                        <a class="btn btn--primary" href="{{ route('rfp.create') }}">Submit a Request for Proposal</a>
                        <a class="btn btn--secondary" href="{{ route('company.delivery-model') }}">Review our capability statement</a>
                    </div>
                    <p class="hero__micro">
                        Structured intake. A qualified engineering lead responds within
                        {{ config('company.response.substantive') }} ({{ config('company.timezone_label') }}).
                    </p>
                </div>

                <div class="hero__visual">
                    <x-figures.system-context />
                </div>
            </div>

            @if ($metrics->isNotEmpty())
                <dl class="metricbar">
                    @foreach ($metrics as $metric)
                        <div class="metricbar__cell">
                            <dd class="metricbar__value">{{ $metric->value }}</dd>
                            <dt class="metricbar__label">{{ $metric->label }}</dt>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>
    </section>

    {{-- ==================================== H-03 — assurance strip ======== --}}
    <section class="assurance" aria-labelledby="assurance-heading">
        <div class="shell">
            <p class="eyebrow assurance__label" id="assurance-heading">Standards and frameworks governing our delivery</p>
            <div class="assurance__grid">
                @foreach ($claims as $claim)
                    <div class="assurance__item">
                        <span class="assurance__standard">{{ $claim->standard }}</span>
                        <span class="assurance__status assurance__status--{{ $claim->status->value }}">{{ $claim->status->label() }}</span>
                    </div>
                @endforeach
            </div>
            <p style="text-align:center;margin-top:1.25rem">
                <a class="textlink" href="{{ route('company.governance') }}">Each claim evidenced in our governance register</a>
            </p>
        </div>
    </section>

    {{-- ==================================== H-04 — holding group ========== --}}
    <section class="section section--tinted" aria-labelledby="group-heading">
        <div class="shell">
            <div class="hero__grid" style="align-items:start">
                <div class="stack">
                    <p class="eyebrow">The group behind the engineering</p>
                    <h2 class="display-2" id="group-heading">We do not only build platforms. We operate them.</h2>
                    <p class="prose-body measure">
                        {{ config('company.legal_name') }} is the technology division of
                        {{ config('company.parent_name') }}, a holding company with operating interests
                        across multiple digital verticals. Chief among them is
                        <strong>{{ config('company.marketplace.name') }}</strong> — a consumer marketplace carrying
                        live M-Pesa payment rails, live fraud exposure and a live uptime obligation to the
                        people trading on it.
                    </p>
                    <p class="prose-body measure">
                        This distinction matters to any institution evaluating an engineering partner. A
                        consultancy that has never carried an on-call rota for its own revenue-bearing system
                        is proposing architecture it has not been accountable for. We carry that rota. The
                        disciplines we sell — observability, incident response, capacity planning, payment
                        reconciliation, PCI scope control — are the disciplines that keep our own group solvent.
                    </p>
                    @if ($platform)
                        <p style="margin-top:.5rem">
                            <a class="textlink" href="{{ route('platforms.show', $platform) }}">Examine the Marketplace architecture</a>
                        </p>
                    @endif
                </div>

                <div class="hairline-grid" style="grid-template-columns:1fr">
                    <div class="cell">
                        <span class="cell__marker">Proof 01</span>
                        <h3 class="cell__title">Live transaction rails</h3>
                        <p class="cell__body">
                            Mobile money, card and bank-transfer settlement in production, with reconciliation
                            and dispute handling — not a sandbox integration.
                        </p>
                    </div>
                    <div class="cell">
                        <span class="cell__marker">Proof 02</span>
                        <h3 class="cell__title">Continuous operation</h3>
                        <p class="cell__body">
                            A production system with a defined recovery objective, tested restores and a
                            documented incident record.
                        </p>
                    </div>
                    <div class="cell">
                        <span class="cell__marker">Proof 03</span>
                        <h3 class="cell__title">Adversarial pressure</h3>
                        <p class="cell__body">
                            Real fraud, real abuse, real scraping. Our security posture is shaped by attacks we
                            have actually absorbed.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================================== H-05 — service matrix ========= --}}
    <section class="section" aria-labelledby="capabilities-heading">
        <div class="shell">
            <div class="section-head">
                <p class="eyebrow">Capability taxonomy</p>
                <h2 class="display-2" id="capabilities-heading">Six engineering disciplines. One accountable delivery organisation.</h2>
                <p class="lede section-head__intro">
                    Enterprise programmes rarely sit inside a single discipline. A payments modernisation is a
                    cloud migration, an API engineering effort and a compliance exercise at the same time. We
                    structure our practice so that one engagement can draw on all six without a change of
                    vendor, contract or accountability.
                </p>
            </div>

            <div class="hairline-grid hairline-grid--3">
                @foreach ($pillars as $pillar)
                    <a class="cell" href="{{ route('pillars.show', $pillar) }}">
                        <span class="cell__marker">Pillar {{ $pillar->reference() }}</span>
                        <h3 class="cell__title">{{ $pillar->title }}</h3>
                        <p class="cell__body">{{ $pillar->thesis }}</p>
                        <ul class="cell__list">
                            @foreach ($pillar->services as $service)
                                <li>{{ $service->title }}</li>
                            @endforeach
                        </ul>
                        <span class="cell__foot">/services/{{ $pillar->slug }} &rarr;</span>
                    </a>
                @endforeach
            </div>

            <p style="margin-top:1.75rem">
                <a class="textlink" href="{{ route('services.index') }}">View the complete service taxonomy</a>
            </p>
        </div>
    </section>

    {{-- ================================= H-06 — engagement models ========= --}}
    <section class="section section--tinted" aria-labelledby="engagement-heading">
        <div class="shell">
            <div class="section-head">
                <p class="eyebrow">How we are engaged</p>
                <h2 class="display-2" id="engagement-heading">Three contracting structures. Chosen by your risk position, not by our preference.</h2>
                <p class="lede section-head__intro">
                    Where the scope is definable and the outcome is fixed, we carry the delivery risk. Where
                    the scope will evolve and your team must own the result, we embed senior engineers inside
                    your organisation. Where the decision precedes the build, we advise and step back.
                </p>
            </div>

            <div class="hairline-grid hairline-grid--3">
                @foreach ($engagementModels as $model)
                    <a class="cell" href="{{ route('engagement-models.show', $model) }}">
                        <span class="cell__marker">{{ $model->reference }}</span>
                        <h3 class="cell__title">{{ $model->title }}</h3>
                        <p class="cell__body">{{ $model->summary }}</p>
                        <ul class="cell__list">
                            <li><strong>Commercial basis:</strong> {{ $model->commercial_basis }}</li>
                            <li><strong>Suited to:</strong> {{ $model->suited_to }}</li>
                        </ul>
                        <span class="cell__foot">Read the contracting terms &rarr;</span>
                    </a>
                @endforeach
            </div>

            <p class="lede" style="margin-top:1.75rem">
                Unsure which applies?
                <a class="textlink" href="{{ route('rfp.create') }}">Our intake determines it in four questions.</a>
            </p>
        </div>
    </section>

    {{-- ================================ H-07 — regional advantage ========= --}}
    <section class="section" aria-labelledby="eldoret-heading">
        <div class="shell">
            <div class="section-head">
                <p class="eyebrow">Operating headquarters — {{ config('company.city') }}, {{ config('company.country') }}</p>
                <h2 class="display-2" id="eldoret-heading">Global engineering standard. Regional economic advantage.</h2>
                <p class="lede section-head__intro">
                    Our location is a deliberate operating decision, not a concession. Eldoret gives us access
                    to a deep, university-fed engineering base at a cost structure no Tier-1 city can match, in
                    a time zone that overlaps the working day of every market we serve. What does not change
                    with geography is the standard: the same architecture review, the same security gates, the
                    same test discipline, the same code we would ship from anywhere.
                </p>
            </div>

            <div class="stack-lg">
                <div class="deflist">
                    <div class="deflist__row">
                        <dt>Advantage 01</dt>
                        <dd>
                            <strong>Structural cost advantage.</strong> Senior engineering capacity at a
                            materially lower blended rate than equivalent onshore or nearshore pods, with no
                            reduction in seniority — we staff engagements with principal and senior engineers,
                            not with volume.
                        </dd>
                    </div>
                    <div class="deflist__row">
                        <dt>Advantage 02</dt>
                        <dd>
                            <strong>Time-zone concurrency.</strong> East Africa Time (UTC+3) gives a working-day
                            overlap with the Gulf, continental Europe, the United Kingdom and South Asia. Our
                            teams work while yours do, so there is no overnight handoff and no 24-hour question
                            latency.
                        </dd>
                    </div>
                    <div class="deflist__row">
                        <dt>Advantage 03</dt>
                        <dd>
                            <strong>Talent density and retention.</strong> Eldoret's universities produce a
                            consistent engineering intake, and our position outside the Nairobi hiring market
                            gives us materially lower attrition than the capital's contested talent pool. For a
                            client, that is continuity of the named engineers who learned your system.
                        </dd>
                    </div>
                </div>

                <x-figures.timezone-overlap />

                <div class="note note--gate">
                    <span class="note__tag">Publication gate G-04 — outstanding</span>
                    <p>
                        Advantage 01 currently states a cost advantage without a figure, because no comparison
                        basis has been supplied. Before launch, either publish the rate differential with its
                        basis (role, region, period) recorded against the metric, or leave the qualitative
                        statement as written. Do not publish a percentage without the basis behind it.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================================= H-08 — governance & SLA ========== --}}
    <section class="section section--tinted" aria-labelledby="governance-heading">
        <div class="shell">
            <div class="section-head">
                <p class="eyebrow">Governance, compliance and service assurance</p>
                <h2 class="display-2" id="governance-heading">Contractual commitments, not aspirations.</h2>
                <p class="lede section-head__intro">
                    Every engagement is governed by a written service framework covering security controls,
                    availability targets, data protection obligations and intellectual property transfer. These
                    terms are available for review before contract, and our security documentation is released
                    to prospective clients under non-disclosure agreement on request.
                </p>
            </div>

            <div class="hairline-grid hairline-grid--4" style="margin-bottom:2rem">
                <div class="cell">
                    <span class="cell__marker">Security</span>
                    <h3 class="cell__title">Security posture</h3>
                    <p class="cell__body">
                        Information security management aligned to ISO/IEC 27001. Role-based access with least
                        privilege, mandatory code review, dependency and secret scanning in CI, annual
                        penetration testing, and a published responsible-disclosure channel.
                    </p>
                </div>
                <div class="cell">
                    <span class="cell__marker">Availability</span>
                    <h3 class="cell__title">Service levels</h3>
                    <p class="cell__body">
                        Three contracted tiers with defined availability targets, severity-based response
                        commitments and a service-credit regime. Measured monthly, reported to the client, and
                        reconciled at quarterly service review.
                    </p>
                </div>
                <div class="cell">
                    <span class="cell__marker">Data</span>
                    <h3 class="cell__title">Data protection</h3>
                    <p class="cell__body">
                        Processing governed by a Data Processing Addendum meeting the Kenya Data Protection Act
                        2019 and GDPR Article 28 requirements. Data residency is selectable by region;
                        sub-processors are disclosed and change-notified.
                    </p>
                </div>
                <div class="cell">
                    <span class="cell__marker">Continuity</span>
                    <h3 class="cell__title">Intellectual property and exit</h3>
                    <p class="cell__body">
                        Client-commissioned work product vests in the client on payment. Source escrow available
                        on request. Every engagement concludes with a documented handover: architecture records,
                        runbooks and credential transfer.
                    </p>
                </div>
            </div>

            <x-site.sla-table />

            <div class="btn-row" style="margin-top:1.75rem">
                <a class="btn btn--secondary" href="{{ route('company.governance') }}">Governance &amp; compliance register</a>
                <a class="btn btn--ghost" href="{{ route('rfp.create', ['track' => \App\Enums\RfpTrack::StrategicAdvisory->value]) }}">Request our security pack</a>
            </div>
        </div>
    </section>

    {{-- ======================================== H-09 — industries ========= --}}
    <section class="section" aria-labelledby="industries-heading">
        <div class="shell">
            <div class="section-head">
                <p class="eyebrow">Sectors we serve</p>
                <h2 class="display-2" id="industries-heading">Domain constraints we already work inside.</h2>
            </div>

            <div class="deflist">
                @foreach ($industries as $industry)
                    <div class="deflist__row">
                        <dt>{{ $industry->title }}</dt>
                        <dd>
                            {{ $industry->constraint_statement }}
                            <br>
                            <a class="textlink" style="display:inline-block;margin-top:.65rem" href="{{ route('industries.show', $industry) }}">Sector detail</a>
                        </dd>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================================== H-10 — insights ========= --}}
    @if ($insights->isNotEmpty())
        <section class="section section--tinted" aria-labelledby="insights-heading">
            <div class="shell">
                <div class="section-head">
                    <p class="eyebrow">Insights</p>
                    <h2 class="display-2" id="insights-heading">Our reasoning, published.</h2>
                    <p class="lede section-head__intro">
                        Architecture decisions, delivery post-mortems and sector analysis from our engineering
                        leadership. We publish the reasoning, including where it proved wrong.
                    </p>
                </div>

                <div class="hairline-grid hairline-grid--3">
                    @foreach ($insights as $insight)
                        <a class="cell" href="{{ route('insights.show', $insight) }}">
                            <span class="cell__marker">{{ $insight->format->label() }}</span>
                            <h3 class="cell__title">{{ $insight->title }}</h3>
                            <p class="cell__body">{{ $insight->abstract_line }}</p>
                            <span class="cell__foot">{{ $insight->read_minutes }} min read &rarr;</span>
                        </a>
                    @endforeach
                </div>

                <p style="margin-top:1.75rem">
                    <a class="textlink" href="{{ route('insights.index') }}">All insights</a>
                </p>
            </div>
        </section>
    @endif

    {{-- =============================================== H-11 — RFP ========= --}}
    <x-site.rfp-band />

</x-layouts.app>
