@php use App\Erp\Enums\TaskStatus; @endphp

<x-layouts.erp title="My work">

    <div class="erp__head">
        <div>
            <h1>{{ str($user->name)->before(' ') }}, here is your board</h1>
            <p class="erp__sub">
                {{ $user->erpRole()?->label() }}@if ($user->job_title) &middot; {{ $user->job_title }}@endif
            </p>
        </div>
        <div class="erp__stats">
            <span><b>{{ $myWork->count() }}</b> assigned</span>
            <span><b>{{ $reviewQueue->count() }}</b> to review</span>
            <span><b>{{ $runningCount }}</b> live projects</span>
        </div>
    </div>

    {{-- Reviews first: this is work already finished by somebody else and
         waiting on the person reading the page. --}}
    @if ($reviewQueue->isNotEmpty())
        <section class="erp__panel erp__panel--attention">
            <h2>Waiting on you to review</h2>
            <table class="erp__table">
                <thead>
                    <tr><th>Task</th><th>Project</th><th>Submitted by</th><th>Waiting</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($reviewQueue as $task)
                        <tr>
                            <td><a href="{{ route('erp.tasks.show', $task) }}"><code>{{ $task->reference }}</code> {{ $task->title }}</a></td>
                            <td>{{ $task->project->name }}</td>
                            <td>{{ $task->assignee?->name ?? '—' }}</td>
                            <td>{{ $task->submitted_at?->diffForHumans() ?? '—' }}</td>
                            <td class="erp__cellright"><a class="erp__btn erp__btn--sm" href="{{ route('erp.tasks.show', $task) }}">Review</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif

    <section class="erp__panel">
        <h2>Your tasks</h2>
        @if ($myWork->isEmpty())
            <p class="erp__empty">Nothing assigned to you. Your lead assigns work from the project board.</p>
        @else
            <table class="erp__table">
                <thead>
                    <tr><th>Task</th><th>Project</th><th>State</th><th>Whose move</th><th>Due</th></tr>
                </thead>
                <tbody>
                    @foreach ($myWork as $task)
                        <tr>
                            <td><a href="{{ route('erp.tasks.show', $task) }}"><code>{{ $task->reference }}</code> {{ $task->title }}</a></td>
                            <td>{{ $task->project->name }}</td>
                            <td><x-erp.state :status="$task->status" /></td>
                            <td class="erp__muted">{{ $task->status->waitingOn() }}</td>
                            <td class="{{ $task->isOverdue() ? 'erp__overdue' : 'erp__muted' }}">
                                {{ $task->due_on?->format('j M') ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>

    @if ($blocked->isNotEmpty())
        <section class="erp__panel erp__panel--negative">
            <h2>Blocked</h2>
            <p class="erp__sub">Somebody has to clear these. They do not move on their own.</p>
            <ul class="erp__list">
                @foreach ($blocked as $task)
                    <li>
                        <a href="{{ route('erp.tasks.show', $task) }}"><code>{{ $task->reference }}</code> {{ $task->title }}</a>
                        <span class="erp__muted">{{ $task->project->name }} &middot; {{ $task->assignee?->name ?? 'unassigned' }}</span>
                        @if ($task->blocked_reason)<em>{{ $task->blocked_reason }}</em>@endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="erp__panel">
        <h2>Projects</h2>
        @if ($projects->isEmpty())
            <p class="erp__empty">You are not on a project yet.</p>
        @else
            <div class="erp__grid">
                @foreach ($projects as $project)
                    @php($p = $project->progress())
                    <a class="erp__card" href="{{ route('erp.projects.show', $project) }}">
                        <span class="erp__code">{{ $project->code }}</span>
                        <h3>{{ $project->name }}</h3>
                        <p class="erp__muted">{{ $project->client ?? 'Internal' }}</p>

                        <div class="erp__meter" role="img"
                             aria-label="{{ $p['percent'] }} per cent of weighted work complete">
                            <span style="width: {{ $p['percent'] }}%"></span>
                        </div>
                        <p class="erp__metertext">
                            <b>{{ $p['percent'] }}%</b>
                            <span class="erp__muted">{{ $p['done'] }} of {{ $p['total'] }} points</span>
                        </p>

                        <p class="erp__chips">
                            <x-erp.state :status="$project->status" />
                            @if (($p['counts'][TaskStatus::InReview->value] ?? 0) > 0)
                                <span class="erp__chip erp__chip--attention">{{ $p['counts'][TaskStatus::InReview->value] }} in review</span>
                            @endif
                            @if (($p['counts'][TaskStatus::Blocked->value] ?? 0) > 0)
                                <span class="erp__chip erp__chip--negative">{{ $p['counts'][TaskStatus::Blocked->value] }} blocked</span>
                            @endif
                            @if ($project->isOverdue())
                                <span class="erp__chip erp__chip--negative">Past target</span>
                            @endif
                        </p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

</x-layouts.erp>
