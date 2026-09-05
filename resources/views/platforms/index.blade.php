{{-- Template T-05 — section index. --}}

<x-layouts.app
    title="Platforms"
    description="Systems the Jiranisoko group operates in production, and the engineering standards they enforce on this division."
>
    <x-site.page-header
        eyebrow="Operator proof"
        heading="Systems we run, not only systems we built."
        standfirst="A consultancy that has never carried an on-call rota for its own revenue-bearing system is proposing architecture it has not been accountable for. These are the systems that hold us to our own standards."
        :crumbs="['Platforms' => null]"
    />

    <section class="section">
        <div class="shell">
            <div class="hairline-grid hairline-grid--2">
                @foreach ($platforms as $platform)
                    <a class="cell" href="{{ route('platforms.show', $platform) }}">
                        <h2 class="cell__title">{{ $platform->title }}</h2>
                        <p class="cell__body">{{ Str::limit($platform->system_context, 240) }}</p>
                        <ul class="taglist" style="margin-top:.75rem">
                            @foreach (array_slice($platform->stack, 0, 5) as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                        <span class="cell__foot">System context &rarr;</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-site.rfp-band />
</x-layouts.app>
