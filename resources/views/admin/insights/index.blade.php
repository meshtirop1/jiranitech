<x-layouts.admin
    title="Insights"
    subtitle="Gate G-08. The homepage rail suppresses itself below three published items — an empty rail undermines the authority claim more than having none."
>
    <x-slot:actions>
        <a class="btn btn--primary btn--sm" href="{{ route('admin.insights.create') }}">New insight</a>
    </x-slot:actions>

    <div class="adm__panel">
        <div class="adm__panelhead">
            <div><h2>All insights</h2></div>
            @php $live = $insights->filter(fn ($i) => $i->published_at && ! $i->published_at->isFuture())->count(); @endphp
            <span class="pill {{ $live >= 3 ? 'pill--closed' : 'pill--open' }}">{{ $live }} published</span>
        </div>

        @forelse ($insights as $insight)
            <div class="adm__row">
                <div class="adm__rowhead">
                    <a class="adm__rowtitle" href="{{ route('admin.insights.edit', $insight) }}">{{ $insight->title }}</a>
                    <span class="pill pill--muted">{{ $insight->format->label() }}</span>
                    @if ($insight->published_at && ! $insight->published_at->isFuture())
                        <span class="pill pill--closed">Live</span>
                    @else
                        <span class="pill pill--open">Draft</span>
                    @endif
                    @if ($insight->pillar)
                        <span class="pill pill--muted">{{ $insight->pillar->nav_title }}</span>
                    @endif
                </div>
                <p style="margin:0;font-size:.85rem;color:var(--ink-2)">{{ $insight->abstract_line }}</p>
                <div class="adm__actions">
                    <a class="btn btn--ghost btn--sm" href="{{ route('admin.insights.edit', $insight) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.insights.destroy', $insight) }}"
                          onsubmit="return confirm('Delete “{{ $insight->title }}” permanently?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--danger btn--sm">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="adm__empty">No insights yet. Create three to make the homepage rail render.</p>
        @endforelse
    </div>

    <div class="adm__panel">
        <div class="adm__panelhead">
            <div>
                <h2>A note on case studies</h2>
                <p>
                    Engineering notes and whitepapers are opinion pieces you can write freely. A case
                    study describes a real engagement — publishing one that did not happen is a
                    fabricated record, and it is the kind of thing a client will recognise.
                </p>
            </div>
        </div>
    </div>
</x-layouts.admin>
