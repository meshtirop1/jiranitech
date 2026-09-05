{{-- Template T-09 — insights index. --}}

<x-layouts.app
    title="Insights"
    description="Architecture decisions, delivery post-mortems and sector analysis from the engineering leadership of Jiranisoko Tech Solutions."
>
    <x-site.page-header
        eyebrow="Insights"
        heading="Our reasoning, published."
        standfirst="Architecture decisions, delivery post-mortems and sector analysis from our engineering leadership. We publish the reasoning, including where it proved wrong."
        :crumbs="['Insights' => null]"
    />

    <section class="section">
        <div class="shell">
            @if ($insights->isEmpty())
                <p class="lede measure">No insights have been published yet.</p>
            @else
                <div class="hairline-grid hairline-grid--3">
                    @foreach ($insights as $insight)
                        <a class="cell" href="{{ route('insights.show', $insight) }}">
                            <span class="cell__marker">{{ $insight->format->label() }}</span>
                            <h2 class="cell__title">{{ $insight->title }}</h2>
                            <p class="cell__body">{{ $insight->abstract_line }}</p>
                            <span class="cell__foot">
                                {{ $insight->published_at->format('j M Y') }} &middot; {{ $insight->read_minutes }} min read &rarr;
                            </span>
                        </a>
                    @endforeach
                </div>

                <div style="margin-top:2rem">
                    {{ $insights->links() }}
                </div>
            @endif
        </div>
    </section>

    <x-site.rfp-band />
</x-layouts.app>
