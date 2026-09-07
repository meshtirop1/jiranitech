{{--
    Template T-12 — careers.

    Section order mirrors the devcom.com career page: hero, breadcrumb strip, intro,
    "why join us" benefit columns, the ladder, a numbered hiring process, open
    positions, closing call to action.

    Publication gate G-10: listings ship unpublished. A job posting is an invitation to
    apply, and advertising a role that is not funded and open wastes a candidate's time.
--}}

<x-layouts.app
    title="Careers"
    description="Engineering roles at Jiranisoko Tech Solutions in Eldoret, Kenya. Principal and senior engineers building systems for banks, government agencies and high-growth ventures."
    :schema="\App\Support\StructuredData::jobPostings($openings)"
>
    <x-site.page-hero
        eyebrow="Company"
        heading="Senior engineering, based in Eldoret"
        subtitle="We staff engagements with principal and senior engineers rather than with volume. That shapes who we hire, how we develop them, and what we can offer someone who is already good."
        :ctaLabel="$openings->isNotEmpty() ? 'See open positions' : 'Send a speculative application'"
        :ctaUrl="$openings->isNotEmpty() ? '#open-positions' : route('contact.engagement-desk')"
        :crumbs="['Company' => route('company.index'), 'Careers' => null]"
    />

    {{-- Intro --}}
    <section class="section">
        <div class="shell">
            <x-site.block-head
                heading="What you would actually work on"
                desc="Core banking migrations, payment rails carrying real money, agent systems inside regulated estates, and platforms our own group depends on to trade. Not a backlog of tickets against someone else's architecture."
            />

            <div class="measure" style="margin-inline:auto;text-align:center">
                <p class="prose-body">
                    Jiranisoko Tech Solutions is the technology division of
                    {{ config('company.parent_name') }}. We build for external clients, and we operate
                    the group's own systems — which means the standards we publish are enforced on us
                    nightly, by software we cannot walk away from. Engineers here carry an on-call rota
                    for something real, and they get the architectural authority that ought to come with it.
                </p>
            </div>
        </div>
    </section>

    {{-- Why join us --}}
    <section class="section section--tinted">
        <div class="shell">
            <x-site.block-head
                heading="Why join us"
                desc="The honest version. These are structural facts about how the firm is set up, not perks."
            />

            <div class="joincols joincols--4">
                <div class="joincol">
                    <span class="joincol__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 3l8 4v5c0 4.5-3.2 7.9-8 9-4.8-1.1-8-4.5-8-9V7l8-4z"/></svg>
                    </span>
                    <div>
                        <h3>Seniority, not volume</h3>
                        <ul class="dotted">
                            <li>Engagements are staffed with principal and senior engineers</li>
                            <li>You are named to a client, not pooled behind an account manager</li>
                            <li>No billable-hours target dictating how you solve a problem</li>
                        </ul>
                    </div>
                </div>

                <div class="joincol">
                    <span class="joincol__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h10M4 18h7"/><path d="M17 15l3 3-3 3"/></svg>
                    </span>
                    <div>
                        <h3>Real systems, real consequences</h3>
                        <ul class="dotted">
                            <li>Production payment rails, auction concurrency, regulated data</li>
                            <li>An on-call rota for software the group actually trades on</li>
                            <li>Post-incident reviews that are published internally, not buried</li>
                        </ul>
                    </div>
                </div>

                <div class="joincol">
                    <span class="joincol__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 20V10M6 20v-6M18 20V4"/></svg>
                    </span>
                    <div>
                        <h3>A published standard</h3>
                        <ul class="dotted">
                            <li>Architecture decision records on every engagement</li>
                            <li>Security scanning that fails a build rather than warning</li>
                            <li>An engineering ladder with written expectations at each level</li>
                        </ul>
                    </div>
                </div>

                <div class="joincol">
                    <span class="joincol__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 21s-7-4.5-7-10a7 7 0 1114 0c0 5.5-7 10-7 10z"/><circle cx="12" cy="11" r="2.5"/></svg>
                    </span>
                    <div>
                        <h3>Based in Eldoret, working globally</h3>
                        <ul class="dotted">
                            <li>Clients across East Africa, the Gulf, Europe and the UK</li>
                            <li>A working day that overlaps those markets without night shifts</li>
                            <li>Outside the Nairobi churn, which is why our teams stay together</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="note" style="margin-top:2rem;max-width:820px;margin-inline:auto">
                <span class="note__tag">Before launch</span>
                <p>
                    This section states structural facts only. If you want to advertise a package —
                    leave, medical cover, learning budget, equipment — add it here once HR has confirmed
                    what is actually on offer. Advertising a benefit that does not exist is discovered at
                    offer stage, with the candidate you most wanted.
                </p>
            </div>
        </div>
    </section>

    {{-- Ladder --}}
    <section class="section">
        <div class="shell">
            <x-site.block-head
                heading="The engineering ladder"
                desc="Four levels, with written expectations. Progression is about the scope of what you can be left to own, not time served."
            />

            <div class="deflist" style="max-width:900px;margin-inline:auto">
                <div class="deflist__row">
                    <dt>Engineer</dt>
                    <dd>Delivers scoped work within an established architecture, with review. Growing depth in one discipline.</dd>
                </div>
                <div class="deflist__row">
                    <dt>Senior engineer</dt>
                    <dd>Owns a component end to end, including its tests, observability and runbook. Reviews other people's work.</dd>
                </div>
                <div class="deflist__row">
                    <dt>Principal engineer</dt>
                    <dd>Owns the architecture of an engagement and is answerable for its non-functional characteristics. Writes the decision records, including the options rejected and why.</dd>
                </div>
                <div class="deflist__row">
                    <dt>Practice lead</dt>
                    <dd>Accountable for technical quality across one of the six disciplines, and for the engineers working in it.</dd>
                </div>
            </div>
        </div>
    </section>

    {{-- Hiring process --}}
    <section class="section section--tinted">
        <div class="shell">
            <x-site.block-head
                heading="How we hire"
                desc="Four steps, roughly two to three weeks. No unpaid take-home project, no whiteboard algorithm round, and we tell you where you stand at every stage."
            />

            <div class="steps">
                <div>
                    <div class="step__num">1</div>
                    <p class="step__desc">Apply to a role that matches what you actually want to do next. A short note beats a long CV.</p>
                </div>
                <div>
                    <div class="step__num">2</div>
                    <p class="step__desc">A conversation with the practice lead about systems you have built and the hardest problem you have owned.</p>
                </div>
                <div>
                    <div class="step__num">3</div>
                    <p class="step__desc">A paid technical session on a realistic problem, timeboxed, with one of our engineers working alongside you.</p>
                </div>
                <div>
                    <div class="step__num">4</div>
                    <p class="step__desc">An offer with the level, the scope and the package written down, and time to consider it.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Open positions --}}
    <section class="section" id="open-positions">
        <div class="shell">
            <x-site.block-head
                heading="Open positions"
                :desc="$openings->isNotEmpty()
                    ? 'Roles currently open in Eldoret. If none of these fit but you would be good here, write to us anyway.'
                    : 'We have no confirmed openings advertised at the moment. That is not the same as not hiring.'"
            />

            @if ($openings->isNotEmpty())
                <div class="vacancies">
                    @foreach ($openings as $opening)
                        <article class="vacancy">
                            <div class="vacancy__head">
                                <h3 class="vacancy__title">{{ $opening->title }}</h3>
                                <ul class="vacancy__meta">
                                    <li>{{ $opening->level }}</li>
                                    <li>{{ $opening->location }}</li>
                                    <li>{{ $opening->arrangement }}</li>
                                    @if ($opening->discipline && isset($pillarsBySlug[$opening->discipline]))
                                        <li>{{ $pillarsBySlug[$opening->discipline]->nav_title }}</li>
                                    @endif
                                </ul>
                            </div>

                            <p class="prose-body">{{ $opening->summary }}</p>

                            <div class="vacancy__cols">
                                <div>
                                    <h4>What you would own</h4>
                                    <ul class="dotted">
                                        @foreach ($opening->responsibilities as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div>
                                    <h4>What we are looking for</h4>
                                    <ul class="dotted">
                                        @foreach ($opening->requirements as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            @if (config('company.email.enquiries'))
                                <p style="margin:.35rem 0 0">
                                    <a class="textlink" href="mailto:{{ config('company.email.enquiries') }}?subject={{ rawurlencode('Application — '.$opening->title) }}">
                                        Apply for this role
                                    </a>
                                </p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <div class="emptystate">
                    <h3>No advertised vacancies right now</h3>
                    <p>
                        We would still like to hear from principal and senior engineers, particularly in
                        payments, cloud architecture and applied AI. Tell us what you have built and the
                        hardest problem you have owned; we keep speculative applications on file and come
                        back to them when a role opens.
                    </p>
                    <div class="btn-row">
                        <a class="btn btn--primary" href="{{ route('contact.engagement-desk') }}">Send a speculative application</a>
                    </div>
                </div>
            @endif

            @if ($draftCount > 0)
                <div class="note note--gate" style="margin-top:1.5rem">
                    <span class="note__tag">Publication gate G-10 — {{ $draftCount }} listing(s) held back</span>
                    <p>
                        {{ $draftCount }} role definition(s) are seeded but unpublished, so they do not appear
                        above. Each is a realistic description of a role this firm would hire for, not a
                        confirmed vacancy. Confirm the role is open and funded, set <code>posted_at</code>,
                        then set <code>is_published</code> on the <code>job_openings</code> row.
                    </p>
                </div>
            @endif
        </div>
    </section>

    {{-- Closing --}}
    <section class="section section--tinted closing">
        <div class="shell">
            <x-site.block-head
                heading="Come and build things that matter"
                desc="If the work described here is the work you want to be doing, start a conversation. We answer every application from a person, not a tracking system."
            />
            <div class="btn-row">
                <a class="btn btn--primary" href="{{ route('contact.engagement-desk') }}">Get in touch</a>
                <a class="btn btn--secondary" href="{{ route('company.delivery-model') }}">How we work</a>
            </div>
        </div>
    </section>
</x-layouts.app>
