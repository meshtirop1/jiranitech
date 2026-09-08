<x-layouts.erp title="Open a project">
    <p class="erp__crumbs"><a href="{{ route('erp.projects.index') }}">Projects</a> <span>/</span> New</p>
    <div class="erp__head"><div><h1>Open a project</h1>
        <p class="erp__sub">A lead is required at creation: a project with nobody able to accept work has no way to finish any.</p></div></div>

    <section class="erp__panel erp__panel--narrow">
        <form method="POST" action="{{ route('erp.projects.store') }}" class="erp__form">
            @csrf
            <label for="name">Project name</label>
            <input id="name" name="name" required maxlength="150" value="{{ old('name') }}">

            <label for="client">Client</label>
            <input id="client" name="client" maxlength="150" value="{{ old('client') }}" placeholder="Leave empty for internal work">

            <label for="summary">Summary</label>
            <textarea id="summary" name="summary" rows="3">{{ old('summary') }}</textarea>

            <label for="lead_id">Lead engineer</label>
            @if ($people->isEmpty())
                {{-- A project cannot be opened without somebody to accept work
                     on it, so say so here rather than present a select with
                     nothing in it attached to a form that will not submit. --}}
                <p class="erp__empty">
                    Nobody is enrolled yet. <a href="{{ route('erp.people.index') }}">Enrol the team</a>
                    first — a project needs a lead who can accept work on it.
                </p>
            @else
                <select id="lead_id" name="lead_id" required>
                    @foreach ($people as $person)
                        <option value="{{ $person->id }}" @selected(old('lead_id') == $person->id)>{{ $person->name }} — {{ $person->erpRole()?->label() }}</option>
                    @endforeach
                </select>
            @endif

            <label for="status">Status</label>
            <select id="status" name="status" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(old('status', 'discovery') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>

            <div class="erp__formrow">
                <div><label for="repository_url">Repository</label>
                    <input id="repository_url" name="repository_url" type="url" value="{{ old('repository_url') }}" placeholder="https://github.com/org/repo"></div>
                <div><label for="default_branch">Default branch</label>
                    <input id="default_branch" name="default_branch" required value="{{ old('default_branch', 'main') }}"></div>
            </div>

            <div class="erp__formrow">
                <div><label for="started_on">Started</label>
                    <input id="started_on" name="started_on" type="date" value="{{ old('started_on') }}"></div>
                <div><label for="target_date">Target</label>
                    <input id="target_date" name="target_date" type="date" value="{{ old('target_date') }}"></div>
            </div>

            <button type="submit" class="erp__btn">Open project</button>
        </form>
    </section>
</x-layouts.erp>
