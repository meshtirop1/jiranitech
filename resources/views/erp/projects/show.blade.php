@php use App\Erp\Enums\TaskStatus; use App\Erp\Enums\ProjectRole; @endphp

<x-layouts.erp :title="$project->name">

    <p class="erp__crumbs">
        <a href="{{ route('erp.projects.index') }}">Projects</a> <span>/</span> <code>{{ $project->code }}</code>
    </p>

    <div class="erp__head">
        <div>
            <h1>{{ $project->name }}</h1>
            <p class="erp__sub">
                <x-erp.state :status="$project->status" />
                <span class="erp__muted">{{ $project->client ?? 'Internal' }}</span>
                @if ($project->repository_url)
                    <a href="{{ $project->repository_url }}" rel="noopener">{{ str($project->repository_url)->after('://') }}</a>
                @endif
            </p>
        </div>
        <div class="erp__stats">
            <span><b>{{ $progress['percent'] }}%</b> complete</span>
            <span><b>{{ $progress['done'] }}/{{ $progress['total'] }}</b> points</span>
            @if ($project->target_date)
                <span class="{{ $project->isOverdue() ? 'erp__overdue' : '' }}"><b>{{ $project->target_date->format('j M Y') }}</b> target</span>
            @endif
        </div>
    </div>

    <div class="erp__meter erp__meter--wide" role="img" aria-label="{{ $progress['percent'] }} per cent of weighted work complete">
        <span style="width: {{ $progress['percent'] }}%"></span>
    </div>
    <p class="erp__sub erp__muted">
        Computed from task state and weight. Nobody types this figure, so it cannot be optimistic.
    </p>

    @if ($project->summary)
        <section class="erp__panel"><p class="erp__prose">{!! nl2br(e($project->summary)) !!}</p></section>
    @endif

    <div class="erp__split">
        <div>
            @forelse ($board as $state => $tasks)
                @php($status = TaskStatus::from($state))
                <section @class(['erp__panel',
                    'erp__panel--attention' => $status === TaskStatus::InReview,
                    'erp__panel--negative' => in_array($status, [TaskStatus::Blocked, TaskStatus::ChangesRequested], true)])>
                    <h2>{{ $status->label() }} <span class="erp__muted">({{ $tasks->count() }})</span></h2>
                    <table class="erp__table">
                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td><a href="{{ route('erp.tasks.show', $task) }}"><code>{{ $task->reference }}</code> {{ $task->title }}</a></td>
                                    <td class="erp__muted">{{ $task->assignee?->name ?? 'Unassigned' }}</td>
                                    <td class="erp__muted">{{ $task->weight }} pt</td>
                                    <td class="{{ $task->isOverdue() ? 'erp__overdue' : 'erp__muted' }}">{{ $task->due_on?->format('j M') ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            @empty
                <section class="erp__panel">
                    <p class="erp__empty">No work on this project yet.</p>
                </section>
            @endforelse
        </div>

        <aside>
            @if ($mayDirect)
                <section class="erp__panel">
                    <h2>Add a task</h2>
                    <form method="POST" action="{{ route('erp.tasks.store', $project) }}" class="erp__form">
                        @csrf
                        <label for="title">Title</label>
                        <input id="title" name="title" required maxlength="200" value="{{ old('title') }}">

                        <label for="description">What is being asked for</label>
                        <textarea id="description" name="description" rows="3">{{ old('description') }}</textarea>

                        <label for="assignee_id">Assign to</label>
                        <select id="assignee_id" name="assignee_id">
                            <option value="">Unassigned</option>
                            @foreach ($assignable as $person)
                                <option value="{{ $person->id }}">{{ $person->name }}</option>
                            @endforeach
                        </select>

                        <div class="erp__formrow">
                            <div>
                                <label for="weight">Points</label>
                                <input id="weight" name="weight" type="number" min="1" max="21" value="{{ old('weight', 1) }}" required>
                            </div>
                            <div>
                                <label for="due_on">Due</label>
                                <input id="due_on" name="due_on" type="date" value="{{ old('due_on') }}">
                            </div>
                        </div>

                        <button type="submit" class="erp__btn">Create task</button>
                    </form>
                </section>
            @endif

            <section class="erp__panel">
                <h2>Team</h2>
                <ul class="erp__list erp__list--plain">
                    @foreach ($project->members as $member)
                        <li>
                            <b>{{ $member->name }}</b>
                            <span class="erp__muted">{{ ProjectRole::from($member->pivot->role)->label() }}</span>
                        </li>
                    @endforeach
                </ul>

                @if ($mayDirect && $candidates->isNotEmpty())
                    <form method="POST" action="{{ route('erp.projects.members.store', $project) }}" class="erp__form">
                        @csrf
                        <label for="user_id">Add someone</label>
                        <select id="user_id" name="user_id" required>
                            @foreach ($candidates as $person)
                                <option value="{{ $person->id }}">{{ $person->name }} — {{ $person->erpRole()?->label() }}</option>
                            @endforeach
                        </select>
                        <label for="role">As</label>
                        <select id="role" name="role" required>
                            @foreach (ProjectRole::cases() as $role)
                                <option value="{{ $role->value }}">{{ $role->label() }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="erp__btn erp__btn--ghost">Add</button>
                    </form>
                @endif
            </section>
        </aside>
    </div>

</x-layouts.erp>
