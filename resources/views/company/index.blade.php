<x-layouts.app title="Company" description="Mandate, governance, leadership, delivery model and careers at Jiranisoko Tech Solutions.">
    <x-site.page-header
        eyebrow="Company"
        heading="Who we are and how we are governed."
        :crumbs="['Company' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell">
            <div class="hairline-grid hairline-grid--2">
                <a class="cell" href="{{ route('company.about') }}">
                    <h2 class="cell__title">About</h2>
                    <p class="cell__body">Our mandate, the standards we hold ourselves to, and our position inside {{ config('company.parent_name') }}.</p>
                </a>
                <a class="cell" href="{{ route('company.governance') }}">
                    <h2 class="cell__title">Governance</h2>
                    <p class="cell__body">Compliance register, service level framework, security controls and data protection obligations.</p>
                </a>
                <a class="cell" href="{{ route('company.delivery-model') }}">
                    <h2 class="cell__title">Delivery model</h2>
                    <p class="cell__body">How an engagement runs from first contact to documented handover, and the SLA framework that governs it.</p>
                </a>
                <a class="cell" href="{{ route('company.leadership') }}">
                    <h2 class="cell__title">Leadership</h2>
                    <p class="cell__body">The executive and principal engineers accountable for delivery.</p>
                </a>
                <a class="cell" href="{{ route('company.careers') }}">
                    <h2 class="cell__title">Careers</h2>
                    <p class="cell__body">Our engineering ladder and current openings in {{ config('company.city') }}.</p>
                </a>
                <a class="cell" href="{{ route('platforms.index') }}">
                    <h2 class="cell__title">Platforms</h2>
                    <p class="cell__body">The systems the group operates in production, and what they enforce on us.</p>
                </a>
            </div>
        </div>
    </section>

    <x-site.rfp-band />
</x-layouts.app>
