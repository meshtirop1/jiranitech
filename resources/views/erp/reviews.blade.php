<x-layouts.erp title="Reviews">
    <div class="erp__head">
        <div>
            <h1>Waiting on you</h1>
            <p class="erp__sub">Work finished by somebody else and submitted for your acceptance. Your own tasks never appear here — nobody reviews their own work.</p>
        </div>
        <div class="erp__stats"><span><b>{{ $queue->count() }}</b> queued</span></div>
    </div>

    @if ($queue->isEmpty())
        <section class="erp__panel"><p class="erp__empty">Nothing to review. Everything submitted has been dealt with.</p></section>
    @else
        <section class="erp__panel erp__panel--attention">
            <table class="erp__table">
                <thead><tr><th>Task</th><th>Project</th><th>Engineer</th><th>Branch</th><th>Waiting</th><th></th></tr></thead>
                <tbody>
                    @foreach ($queue as $task)
                        <tr>
                            <td><a href="{{ route('erp.tasks.show', $task) }}"><code>{{ $task->reference }}</code> {{ $task->title }}</a></td>
                            <td>{{ $task->project->name }}</td>
                            <td>{{ $task->assignee?->name ?? '—' }}</td>
                            <td><code class="erp__muted">{{ $task->branch ?? '—' }}</code></td>
                            <td>{{ $task->submitted_at?->diffForHumans() ?? '—' }}</td>
                            <td class="erp__cellright"><a class="erp__btn erp__btn--sm" href="{{ route('erp.tasks.show', $task) }}">Open</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif
</x-layouts.erp>
