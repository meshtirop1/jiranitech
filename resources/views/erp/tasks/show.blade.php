@php
    use App\Erp\Enums\TaskStatus;
    $user = auth()->user();
    $isAssignee = $task->assignee_id === $user->id;
    $mayEdit = $isAssignee || $task->project->mayBeDirectedBy($user);
@endphp

<x-layouts.erp :title="$task->reference">

    <p class="erp__crumbs">
        <a href="{{ route('erp.projects.show', $task->project) }}">{{ $task->project->name }}</a>
        <span>/</span> <code>{{ $task->reference }}</code>
    </p>

    <div class="erp__head">
        <div>
            <h1>{{ $task->title }}</h1>
            <p class="erp__sub">
                <x-erp.state :status="$task->status" />
                <span class="erp__muted">Waiting on {{ strtolower($task->status->waitingOn()) }}</span>
            </p>
        </div>
        <div class="erp__stats">
            <span><b>{{ $task->assignee?->name ?? 'Unassigned' }}</b> assignee</span>
            <span><b>{{ $task->weight }}</b> points</span>
            @if ($task->due_on)
                <span class="{{ $task->isOverdue() ? 'erp__overdue' : '' }}"><b>{{ $task->due_on->format('j M') }}</b> due</span>
            @endif
        </div>
    </div>

    {{-- What is stopping this moving, said plainly. The controls below are shown
         either way: hiding a button teaches nobody why it is unavailable. --}}
    @if ($blockedBy)
        <p class="erp__note">{{ $blockedBy }}</p>
    @endif

    <div class="erp__split">
        <div>
            @if ($task->description)
                <section class="erp__panel">
                    <h2>What was asked for</h2>
                    <p class="erp__prose">{!! nl2br(e($task->description)) !!}</p>
                </section>
            @endif

            <section class="erp__panel">
                <h2>Engineer's documentation</h2>
                <p class="erp__sub">Required before this task can go to review. What you did, what you decided, and anything the reviewer would otherwise have to ask.</p>

                @if ($mayEdit)
                    <form method="POST" action="{{ route('erp.tasks.update', $task) }}" class="erp__form">
                        @csrf @method('PUT')
                        @if ($task->project->mayBeDirectedBy($user))
                            <input type="hidden" name="title" value="{{ $task->title }}">
                            <input type="hidden" name="weight" value="{{ $task->weight }}">
                            <input type="hidden" name="assignee_id" value="{{ $task->assignee_id }}">
                            <input type="hidden" name="due_on" value="{{ $task->due_on?->toDateString() }}">
                            <input type="hidden" name="description" value="{{ $task->description }}">
                        @endif

                        <label for="documentation">Documentation</label>
                        <textarea id="documentation" name="documentation" rows="10"
                                  placeholder="Approach taken, decisions made and why, anything rejected, how to verify it.">{{ old('documentation', $task->documentation) }}</textarea>

                        <div class="erp__formrow">
                            <div>
                                <label for="branch">Branch</label>
                                <input id="branch" name="branch" value="{{ old('branch', $task->branch) }}" placeholder="feature/{{ strtolower($task->reference) }}">
                            </div>
                            <div>
                                <label for="pull_request_url">Pull request</label>
                                <input id="pull_request_url" name="pull_request_url" type="url"
                                       value="{{ old('pull_request_url', $task->pull_request_url) }}" placeholder="https://github.com/...">
                            </div>
                        </div>

                        @if ($task->status === TaskStatus::Blocked || $task->blocked_reason)
                            <label for="blocked_reason">What is blocking it</label>
                            <input id="blocked_reason" name="blocked_reason" value="{{ old('blocked_reason', $task->blocked_reason) }}">
                        @endif

                        <button type="submit" class="erp__btn">Save</button>
                    </form>
                @elseif ($task->documentation)
                    <p class="erp__prose">{!! nl2br(e($task->documentation)) !!}</p>
                @else
                    <p class="erp__empty">Not written yet.</p>
                @endif
            </section>

            @if ($task->reviews->isNotEmpty())
                <section class="erp__panel">
                    <h2>Review history</h2>
                    @foreach ($task->reviews as $review)
                        <div class="erp__review">
                            <p>
                                <b>{{ $review->reviewer->name }}</b>
                                <span class="erp__chip erp__chip--{{ $review->approved() ? 'positive' : 'negative' }}">
                                    {{ $review->approved() ? 'Approved' : 'Changes requested' }}
                                </span>
                                <span class="erp__muted">{{ $review->created_at->format('j M Y, H:i') }}</span>
                            </p>
                            @if ($review->notes)<p class="erp__prose">{!! nl2br(e($review->notes)) !!}</p>@endif
                        </div>
                    @endforeach
                </section>
            @endif

            <section class="erp__panel">
                <h2>Updates</h2>
                @forelse ($task->updates as $update)
                    <div class="erp__review">
                        <p><b>{{ $update->user->name }}</b> <span class="erp__muted">{{ $update->created_at->diffForHumans() }}</span></p>
                        <p class="erp__prose">{!! nl2br(e($update->body)) !!}</p>
                    </div>
                @empty
                    <p class="erp__empty">No updates yet.</p>
                @endforelse

                <form method="POST" action="{{ route('erp.tasks.comment', $task) }}" class="erp__form">
                    @csrf
                    <label for="body">Add an update</label>
                    <textarea id="body" name="body" rows="3" required></textarea>
                    <button type="submit" class="erp__btn erp__btn--ghost">Post</button>
                </form>
            </section>
        </div>

        <aside>
            <section class="erp__panel">
                <h2>Move this task</h2>
                @if (empty($available))
                    <p class="erp__empty">Nothing you can do here right now.</p>
                @else
                    @foreach ($available as $state)
                        <form method="POST" action="{{ route('erp.tasks.transition', $task) }}" class="erp__form erp__form--tight">
                            @csrf
                            <input type="hidden" name="to" value="{{ $state->value }}">

                            @if ($state === TaskStatus::ChangesRequested)
                                <label for="note-{{ $state->value }}">What needs changing</label>
                                <textarea id="note-{{ $state->value }}" name="note" rows="3" required
                                          placeholder="Be specific. This is what the engineer works from."></textarea>
                            @elseif ($state === TaskStatus::Done)
                                <label for="note-{{ $state->value }}">Merge reference</label>
                                <input id="note-{{ $state->value }}" name="note" placeholder="commit sha or MR number">
                            @elseif ($state === TaskStatus::Approved)
                                <label for="note-{{ $state->value }}">Note (optional)</label>
                                <input id="note-{{ $state->value }}" name="note">
                            @endif

                            <button type="submit" @class([
                                'erp__btn',
                                'erp__btn--positive' => in_array($state, [TaskStatus::Approved, TaskStatus::Done], true),
                                'erp__btn--negative' => $state === TaskStatus::ChangesRequested,
                            ])>
                                {{ match ($state) {
                                    TaskStatus::InProgress => 'Start work',
                                    TaskStatus::InReview => 'Submit for review',
                                    TaskStatus::Approved => 'Approve',
                                    TaskStatus::ChangesRequested => 'Request changes',
                                    TaskStatus::Done => 'Record merge and close',
                                    TaskStatus::Blocked => 'Mark blocked',
                                    TaskStatus::Backlog => 'Return to backlog',
                                    TaskStatus::Cancelled => 'Cancel',
                                } }}
                            </button>
                        </form>
                    @endforeach
                @endif
            </section>

            <section class="erp__panel">
                <h2>Repository</h2>
                <dl class="erp__dl">
                    <dt>Project repo</dt>
                    <dd>@if ($task->project->repository_url)<a href="{{ $task->project->repository_url }}" rel="noopener">{{ str($task->project->repository_url)->after('://') }}</a>@else <span class="erp__muted">Not set</span> @endif</dd>
                    <dt>Branch</dt>
                    <dd>{{ $task->branch ? '' : '' }}<code>{{ $task->branch ?? '—' }}</code></dd>
                    <dt>Pull request</dt>
                    <dd>@if ($task->pull_request_url)<a href="{{ $task->pull_request_url }}" rel="noopener">Open</a>@else <span class="erp__muted">—</span> @endif</dd>
                    <dt>Merged as</dt>
                    <dd><code>{{ $task->merge_reference ?? '—' }}</code></dd>
                </dl>
            </section>

            <section class="erp__panel">
                <h2>Trail</h2>
                <ol class="erp__trail">
                    @foreach ($activity as $entry)
                        <li>
                            <span>{{ $entry->describe() }}</span>
                            <em>{{ $entry->created_at?->format('j M Y, H:i') }}</em>
                            @if ($entry->note)<span class="erp__muted">{{ $entry->note }}</span>@endif
                        </li>
                    @endforeach
                </ol>
            </section>
        </aside>
    </div>

</x-layouts.erp>
