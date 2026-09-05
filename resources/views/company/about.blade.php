{{-- Template T-11 — corporate governance and about. --}}

<x-layouts.app
    title="About"
    description="Jiranisoko Tech Solutions is the enterprise technology division of Jiranisoko Market Ltd, operating from Eldoret, Kenya."
>
    <x-site.page-header
        eyebrow="Mandate and heritage"
        heading="An engineering division inside an operating group."
        :crumbs="['Company' => route('company.index'), 'About' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell">
            <div class="stack-lg" style="max-width:820px">
                <div class="stack">
                    <h2 class="display-3">Our mandate</h2>
                    <p class="prose-body measure">
                        {{ config('company.legal_name') }} exists to deliver software engineering of a standard
                        normally procured from European and North American systems integrators, from an
                        operating base in {{ config('company.city') }}, {{ config('company.country') }}. We were
                        established as the technology division of {{ config('company.parent_name') }} to build
                        and operate the group's own digital platforms, and we now take that same capability to
                        external clients.
                    </p>
                    <p class="prose-body measure">
                        That order matters. We did not start as a consultancy looking for systems to build. We
                        started as the team accountable for systems that had to stay up, and the standards we
                        sell are the ones we already had to meet.
                    </p>
                </div>

                <div class="stack">
                    <h2 class="display-3">What we hold ourselves to</h2>
                    <div class="deflist">
                        <div class="deflist__row">
                            <dt>Evidence over assertion</dt>
                            <dd>Every figure we publish carries the basis it rests on, and every compliance claim carries its status. Where we follow a standard but do not hold the certificate, we say so on the page.</dd>
                        </div>
                        <div class="deflist__row">
                            <dt>The unwelcome answer</dt>
                            <dd>Where a database would beat a ledger, where a monolith should not be decomposed, where retrieval would outperform fine-tuning at a fraction of the cost — we say so, including when it reduces the engagement.</dd>
                        </div>
                        <div class="deflist__row">
                            <dt>Seniority, not volume</dt>
                            <dd>We staff engagements with principal and senior engineers. Our cost advantage comes from where we operate, not from who we put on the work.</dd>
                        </div>
                        <div class="deflist__row">
                            <dt>Exit designed in</dt>
                            <dd>Work product vests in the client on payment, source escrow is available, and every engagement closes with a documented handover. A client who cannot leave is not a client we have earned.</dd>
                        </div>
                    </div>
                </div>

                <div class="stack" id="group">
                    <h2 class="display-3">The group</h2>
                    <p class="prose-body measure">
                        {{ config('company.parent_name') }} is a holding company with operating interests
                        across several digital verticals, chief among them Jiranisoko Marketplace. The group
                        structure gives this division two things a standalone consultancy of comparable size
                        does not have: a balance sheet behind its contractual commitments, and a production
                        system of its own that enforces its engineering standards daily.
                    </p>
                    <p>
                        <a class="textlink" href="{{ route('platforms.index') }}">The platforms we operate</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <x-site.rfp-band />
</x-layouts.app>
