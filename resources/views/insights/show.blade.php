{{-- Template T-10 — article. --}}

<x-layouts.app
    :title="$insight->title"
    :description="$insight->abstract_line"
    :schema="[\App\Support\StructuredData::article($insight)]"
>
    <x-site.page-header
        :eyebrow="$insight->format->label()"
        :heading="$insight->title"
        :standfirst="$insight->abstract_line"
        :crumbs="['Insights' => route('insights.index'), $insight->title => null]"
    />

    <section class="section" style="padding-top:1.5rem">
        <div class="shell">
            <p class="mono muted small" style="margin-bottom:2rem">
                Published {{ $insight->published_at->format('j F Y') }}
                &middot; {{ $insight->read_minutes }} minute read
                @if ($insight->pillar)
                    &middot; <a href="{{ route('pillars.show', $insight->pillar) }}">{{ $insight->pillar->nav_title }}</a>
                @endif
            </p>

            <div class="article-body">
                @foreach (preg_split('/\n\s*\n/', (string) $insight->body) as $paragraph)
                    @if (trim($paragraph) !== '')
                        <p>{{ trim($paragraph) }}</p>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="section section--tinted">
            <div class="shell">
                <div class="section-head">
                    <p class="eyebrow">Also published</p>
                    <h2 class="display-3">Related reading</h2>
                </div>
                <div class="hairline-grid hairline-grid--2">
                    @foreach ($related as $other)
                        <a class="cell" href="{{ route('insights.show', $other) }}">
                            <span class="cell__marker">{{ $other->format->label() }}</span>
                            <h3 class="cell__title">{{ $other->title }}</h3>
                            <p class="cell__body">{{ $other->abstract_line }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.rfp-band />
</x-layouts.app>
