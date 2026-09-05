{{-- Template T-05 — section index. --}}

<x-layouts.app
    title="Industries"
    description="Sector constraints we already work inside — banking and financial services, public sector, commerce and marketplaces, and high-growth ventures."
>
    <x-site.page-header
        eyebrow="Sectors we serve"
        heading="Domain constraints we already work inside."
        standfirst="A banking chief information officer does not search for multi-tenant SaaS engineering. These pages map sector vocabulary onto our capability taxonomy."
        :crumbs="['Industries' => null]"
    />

    <section class="section">
        <div class="shell">
            <div class="hairline-grid hairline-grid--2">
                @foreach ($industries as $industry)
                    <a class="cell" href="{{ route('industries.show', $industry) }}">
                        <h2 class="cell__title">{{ $industry->title }}</h2>
                        <p class="cell__body">{{ $industry->constraint_statement }}</p>
                        <ul class="cell__list" style="margin-top:.6rem">
                            @foreach (array_slice($industry->pressures, 0, 3) as $pressure)
                                <li>{{ $pressure }}</li>
                            @endforeach
                        </ul>
                        <span class="cell__foot">Sector detail &rarr;</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-site.rfp-band />
</x-layouts.app>
