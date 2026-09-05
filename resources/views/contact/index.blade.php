<x-layouts.app
    title="Contact"
    description="Structured enquiry routes to Jiranisoko Tech Solutions — request for proposal, engagement desk, and security disclosure."
>
    <x-site.page-header
        eyebrow="Contact"
        heading="Three routes in, each answered by a different person."
        :crumbs="['Contact' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell">
            <div class="hairline-grid hairline-grid--3">
                <a class="cell" href="{{ route('rfp.create') }}">
                    <span class="cell__marker">Route 01</span>
                    <h2 class="cell__title">Request for Proposal</h2>
                    <p class="cell__body">A structured intake that routes your enquiry to the engineering lead who owns the relevant practice. Use this when you have a requirement.</p>
                    <span class="cell__foot">Structured intake &rarr;</span>
                </a>
                <a class="cell" href="{{ route('contact.engagement-desk') }}">
                    <span class="cell__marker">Route 02</span>
                    <h2 class="cell__title">Engagement desk</h2>
                    <p class="cell__body">General enquiries, partnership discussions, and anything that is not yet a requirement. Answered by a person, not a queue.</p>
                    <span class="cell__foot">Desk details &rarr;</span>
                </a>
                <a class="cell" href="{{ route('legal.disclosure') }}">
                    <span class="cell__marker">Route 03</span>
                    <h2 class="cell__title">Security disclosure</h2>
                    <p class="cell__body">For security researchers reporting a vulnerability in a system we operate. Reports are acknowledged and triaged, not ignored.</p>
                    <span class="cell__foot">Disclosure policy &rarr;</span>
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
