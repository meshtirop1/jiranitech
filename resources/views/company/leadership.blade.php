{{--
    Template T-11 — leadership.

    Structure follows the devcom.com page idiom: dark overlay hero, breadcrumb strip,
    then sections led by a centred uppercase heading over a centred descriptor,
    alternating between the base and tinted grounds.

    Publication gate G-09: each card states a post and its accountability, which are
    facts about how the division is run. Personal names and photographs are supplied by
    the client, with consent, before launch. Cards render either way.
--}}

<x-layouts.app
    title="Leadership"
    description="The posts accountable for delivery at Jiranisoko Tech Solutions — executive, security, data protection and the practice leads who own each engineering discipline."
>
    <x-site.page-hero
        eyebrow="Company"
        heading="The people answerable for the work"
        subtitle="Institutional buyers ask who is accountable before they ask what you build. This page names the posts, what each one owns, and where escalation reaches."
        ctaLabel="Speak with the engagement desk"
        :ctaUrl="route('contact.engagement-desk')"
        :crumbs="['Company' => route('company.index'), 'Leadership' => null]"
    />

    {{-- Operating principle --}}
    <section class="section">
        <div class="shell">
            <x-site.block-head
                heading="Accountability is a named post"
                desc="We publish who owns what because a client escalating an issue should never have to discover the org chart by trial and error. Every engagement has a named executive sponsor and a named practice lead from the first scoping call."
            />

            <div class="joincols joincols--3">
                <div class="joincol">
                    <span class="joincol__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 3l8 4v5c0 4.5-3.2 7.9-8 9-4.8-1.1-8-4.5-8-9V7l8-4z"/></svg>
                    </span>
                    <div>
                        <h3>Independent gates</h3>
                        <ul class="dotted">
                            <li>Security review can halt a production release</li>
                            <li>Data protection sits outside delivery and reports to the board</li>
                            <li>Neither gate can be overridden by a delivery deadline</li>
                        </ul>
                    </div>
                </div>
                <div class="joincol">
                    <span class="joincol__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M4 20V9M10 20V5M16 20v-7M22 20H2"/></svg>
                    </span>
                    <div>
                        <h3>One escalation path</h3>
                        <ul class="dotted">
                            <li>Practice lead for anything technical</li>
                            <li>Director of Delivery for scope, schedule or service level</li>
                            <li>Managing Director as the final point, without a queue</li>
                        </ul>
                    </div>
                </div>
                <div class="joincol">
                    <span class="joincol__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/><path d="M3 12h3M18 12h3M12 3v3M12 18v3"/></svg>
                    </span>
                    <div>
                        <h3>Continuity</h3>
                        <ul class="dotted">
                            <li>Named engineers are not rotated without your consent</li>
                            <li>Bench cover is our obligation, not your risk</li>
                            <li>Handover is contractual at the close of every engagement</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- The posts --}}
    <section class="section section--tinted">
        <div class="shell">
            <x-site.block-head
                heading="Executive and technical leadership"
                desc="Eight posts carry the division. Five are firm-wide; three lead the engineering disciplines that most engagements draw on first."
            />

            @if ($leadership->isEmpty())
                <div class="emptystate">
                    <h3>Leadership has not been published</h3>
                    <p>Run the database seeders to populate the posts, then supply the appointee for each.</p>
                </div>
            @else
                <div class="people">
                    @foreach ($leadership as $member)
                        <article class="person">
                            <div class="person__top">
                                <span class="person__avatar @unless ($member->isAnnounced()) person__avatar--vacant @endunless" aria-hidden="true">
                                    {{ $member->initials() }}
                                </span>
                                <div>
                                    @if ($member->isAnnounced())
                                        <h3 class="person__name">{{ $member->name }}</h3>
                                    @else
                                        <h3 class="person__name person__name--pending">Appointment to be announced</h3>
                                    @endif
                                    <p class="person__role">{{ $member->role_title }}</p>
                                </div>
                            </div>

                            <p class="person__accountability">{{ $member->accountability }}</p>

                            <div class="person__remit">
                                <ul class="dotted">
                                    @foreach ($member->remit as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            @if ($member->discipline && isset($pillarsBySlug[$member->discipline]))
                                <p style="margin:0">
                                    <a class="textlink" href="{{ route('pillars.show', $pillarsBySlug[$member->discipline]) }}">
                                        {{ $pillarsBySlug[$member->discipline]->nav_title }}
                                    </a>
                                </p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif

            @if ($pendingAppointments > 0)
            @endif
        </div>
    </section>

    {{-- Governance link --}}
    <section class="section closing">
        <div class="shell">
            <x-site.block-head
                heading="How this is governed"
                desc="Accountability on a page is worth what the framework behind it is worth. The governance register carries the compliance status, service level framework and data protection obligations these posts are answerable for."
            />
            <div class="btn-row">
                <a class="btn btn--primary" href="{{ route('company.governance') }}">Governance register</a>
                <a class="btn btn--secondary" href="{{ route('company.delivery-model') }}">Delivery model</a>
            </div>
        </div>
    </section>

    <x-site.rfp-band />
</x-layouts.app>
