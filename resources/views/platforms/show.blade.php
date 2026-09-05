{{--
    Template T-08 — platform reference.

    Publication gate G-03: scale characteristics are commercially sensitive and render
    only once the group has cleared them for external disclosure. The qualitative
    operator argument stands on its own without them.
--}}

<x-layouts.app :title="$platform->title" :description="Str::limit($platform->system_context, 300)">
    <x-site.page-header
        eyebrow="Platform reference"
        :heading="$platform->title"
        :crumbs="['Platforms' => route('platforms.index'), $platform->title => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell stack-lg">
            <p class="lede measure" style="font-size:1.15rem">{{ $platform->system_context }}</p>

            <div>
                <h2 class="eyebrow" style="margin-bottom:1rem">Scale characteristics</h2>

                @if ($platform->cleared_for_disclosure)
                    <div class="deflist">
                        @foreach ($platform->scale_metrics as $metric)
                            <div class="deflist__row">
                                <dt>{{ $metric['label'] }}</dt>
                                <dd>{{ $metric['value'] }}</dd>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="note note--gate">
                        <span class="note__tag">Publication gate G-03 — outstanding</span>
                        <p>
                            Scale characteristics for this platform have not been cleared for external
                            disclosure by {{ config('company.parent_name') }}. They are available to
                            prospective clients under a mutual non-disclosure agreement. Set
                            <code>cleared_for_disclosure</code> on this record once written clearance is held,
                            and replace the placeholder values with the figures that were cleared.
                        </p>
                    </div>
                @endif
            </div>

            <div>
                <h2 class="eyebrow" style="margin-bottom:.75rem">Operating stack</h2>
                <ul class="taglist">
                    @foreach ($platform->stack as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="note">
                <span class="note__tag">Entity note</span>
                <p>
                    {{ $platform->title }} is operated by {{ config('company.parent.name') }}, the holding
                    company. It is a sibling of this division — not its parent, and not work delivered
                    through our client practice. We reference it because we are accountable for running it.
                </p>
            </div>

            <div class="btn-row">
                @if ($platform->external_url)
                    <a class="btn btn--secondary" href="{{ $platform->external_url }}" rel="noopener" target="_blank">
                        {{ $platform->external_label ?? 'Visit the platform' }}
                    </a>
                @endif
                <a class="btn btn--primary" href="{{ route('rfp.create') }}">Request the platform briefing under NDA</a>
                <a class="btn btn--ghost" href="{{ route('services.index') }}">The disciplines behind it</a>
            </div>
        </div>
    </section>

    <x-site.rfp-band />
</x-layouts.app>
