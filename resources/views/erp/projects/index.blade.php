@php use App\Erp\Enums\TaskStatus; @endphp

<x-layouts.erp title="Projects">
    <div class="erp__head">
        <div><h1>Projects</h1><p class="erp__sub">Progress is computed from task state, never entered by hand.</p></div>
        @if (auth()->user()->erpRole()?->opensProjects())
            <a class="erp__btn" href="{{ route('erp.projects.create') }}">Open a project</a>
        @endif
    </div>

    @if ($projects->isEmpty())
        <section class="erp__panel"><p class="erp__empty">No projects visible to you.</p></section>
    @else
        <div class="erp__grid">
            @foreach ($projects as $project)
                @php($p = $project->progress())
                <a class="erp__card" href="{{ route('erp.projects.show', $project) }}">
                    <span class="erp__code">{{ $project->code }}</span>
                    <h3>{{ $project->name }}</h3>
                    <p class="erp__muted">{{ $project->client ?? 'Internal' }}</p>
                    <div class="erp__meter"><span style="width: {{ $p['percent'] }}%"></span></div>
                    <p class="erp__metertext"><b>{{ $p['percent'] }}%</b>
                        <span class="erp__muted">{{ $p['done'] }} of {{ $p['total'] }} points</span></p>
                    <p class="erp__chips">
                        <x-erp.state :status="$project->status" />
                        @if (($p['counts'][TaskStatus::InReview->value] ?? 0) > 0)
                            <span class="erp__chip erp__chip--attention">{{ $p['counts'][TaskStatus::InReview->value] }} in review</span>
                        @endif
                        @if (($p['counts'][TaskStatus::Blocked->value] ?? 0) > 0)
                            <span class="erp__chip erp__chip--negative">{{ $p['counts'][TaskStatus::Blocked->value] }} blocked</span>
                        @endif
                    </p>
                </a>
            @endforeach
        </div>
    @endif
</x-layouts.erp>
