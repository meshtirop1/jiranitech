<x-layouts.admin
    title="Dashboard"
    subtitle="The ten publication gates, read live from the site itself rather than from a checklist. Anything open is either hidden from visitors or rendering a visible marker."
>
    <div class="adm__stats">
        <div class="adm__stat"><b>{{ $openGates }}</b><span>Gates open</span></div>
        <div class="adm__stat"><b>{{ $blockingGates }}</b><span>Blocking launch</span></div>
        <div class="adm__stat"><b>{{ $counts['services'] }}</b><span>Service pages</span></div>
        <div class="adm__stat"><b>{{ $counts['insights'] }}</b><span>Insights live</span></div>
        <div class="adm__stat"><b>{{ $counts['jobs'] }}</b><span>Vacancies live</span></div>
        <div class="adm__stat"><b>{{ $counts['rfp'] }}</b><span>RFP enquiries</span></div>
    </div>

    <div class="adm__panel">
        <div class="adm__panelhead">
            <div>
                <h2>Publication gates</h2>
                <p>
                    Each gate is enforced in code, so this reflects what the site is actually doing.
                    Closing one here changes the public pages immediately.
                </p>
            </div>
            @if ($openGates === 0)
                <span class="pill pill--closed">All closed</span>
            @else
                <span class="pill pill--open">{{ $openGates }} open</span>
            @endif
        </div>

        <div class="gates">
            @foreach ($gates as $gate)
                <article class="gate @if ($gate['closed']) gate--closed @endif">
                    <span class="gate__stripe" aria-hidden="true"></span>
                    <span class="gate__id">{{ $gate['id'] }}</span>
                    <div class="gate__body">
                        <h3>{{ $gate['title'] }}</h3>
                        <p>{{ $gate['detail'] }}</p>
                        <span class="gate__where">Appears on: {{ $gate['where'] }}</span>
                    </div>
                    <div class="gate__side">
                        @if ($gate['closed'])
                            <span class="pill pill--closed">Closed</span>
                        @elseif ($gate['blocking'])
                            <span class="pill pill--blocking">Blocks launch</span>
                        @else
                            <span class="pill pill--open">Open</span>
                        @endif

                        @if ($gate['route'] && ! $gate['closed'])
                            <a class="btn btn--secondary btn--sm" href="{{ route($gate['route']) }}">Resolve</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    <div class="adm__panel">
        <div class="adm__panelhead">
            <div>
                <h2>Latest enquiries</h2>
                <p>Submissions from the Request for Proposal form.</p>
            </div>
            <a class="btn btn--ghost btn--sm" href="{{ route('admin.rfp.index') }}">Open inbox</a>
        </div>

        @forelse ($recentRfp as $submission)
            <div class="adm__row">
                <div class="adm__rowhead">
                    <a class="adm__rowtitle" href="{{ route('admin.rfp.show', $submission) }}">{{ $submission->organisation }}</a>
                    <span class="pill pill--muted">{{ $submission->track->label() }}</span>
                    @if ($submission->nda_required)
                        <span class="pill pill--open">NDA requested</span>
                    @endif
                    @unless ($submission->acknowledged_at)
                        <span class="pill pill--blocking">Unacknowledged</span>
                    @endunless
                </div>
                <p style="margin:0;font-size:.85rem;color:var(--muted)">
                    {{ $submission->reference }} &middot; {{ $submission->contact_name }} &middot;
                    {{ $submission->created_at->diffForHumans() }}
                </p>
            </div>
        @empty
            <p class="adm__empty">No enquiries yet.</p>
        @endforelse
    </div>
</x-layouts.admin>
