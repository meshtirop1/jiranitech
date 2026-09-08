@php use App\Erp\Enums\ErpRole; @endphp

<x-layouts.erp title="People">
    <div class="erp__head">
        <div>
            <h1>People</h1>
            <p class="erp__sub">
                The posts published at
                <a href="https://jiranisokotech.co.ke/company/leadership" rel="noopener">company/leadership</a>,
                and what each one may do here. A site administrator is not automatically an engineer.
            </p>
        </div>
        <div class="erp__stats">
            <span><b>{{ $leadership->count() }}</b> leadership</span>
            <span><b>{{ $engineers->count() }}</b> engineers</span>
        </div>
    </div>

    <div class="erp__split">
        <div>
            <section class="erp__panel">
                <h2>Leadership</h2>
                <table class="erp__table">
                    <thead><tr><th>Name</th><th>Post</th><th>Authority</th><th>Status</th>@if ($mayChangeRoles)<th></th>@endif</tr></thead>
                    <tbody>
                        @foreach ($leadership as $person)
                            <tr>
                                <td><b>{{ $person->name }}</b><br><span class="erp__muted">{{ $person->email }}</span></td>
                                <td>{{ $person->job_title ?: $person->erpRole()?->label() }}<br>
                                    <span class="erp__muted">{{ $person->erpRole()?->label() }}</span></td>
                                <td class="erp__muted">
                                    @php($r = $person->erpRole())
                                    <span class="erp__chips">
                                        @if ($r?->seesEverything())<span class="erp__chip erp__chip--neutral">Sees all</span>@endif
                                        @if ($r?->opensProjects())<span class="erp__chip erp__chip--attention">Opens projects</span>@endif
                                        @if ($r?->reviewsAnywhere())<span class="erp__chip erp__chip--attention">Reviews anywhere</span>@endif
                                        @if ($r?->enrolsPeople())<span class="erp__chip erp__chip--neutral">Enrols</span>@endif
                                        @if (! $r?->mayHoldWork())<span class="erp__chip erp__chip--muted">Takes no work</span>@endif
                                    </span>
                                </td>
                                <td>@if ($person->is_active)<span class="erp__chip erp__chip--positive">Active</span>@else<span class="erp__chip erp__chip--muted">Suspended</span>@endif</td>
                                @if ($mayChangeRoles)
                                    <td class="erp__cellright">
                                        <form method="POST" action="{{ route('erp.people.update', $person) }}" class="erp__inlineform">
                                            @csrf @method('PUT')
                                            <select name="erp_role">
                                                @foreach (ErpRole::cases() as $role)
                                                    <option value="{{ $role->value }}" @selected($person->erpRole() === $role)>{{ $role->label() }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="job_title" value="{{ $person->job_title }}">
                                            <select name="is_active">
                                                <option value="1" @selected($person->is_active)>Active</option>
                                                <option value="0" @selected(! $person->is_active)>Suspended</option>
                                            </select>
                                            <button type="submit" class="erp__btn erp__btn--sm erp__btn--ghost">Save</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="erp__panel">
                <h2>Engineers</h2>
                @if ($engineers->isEmpty())
                    <p class="erp__empty">No engineers enrolled yet.</p>
                @else
                    <table class="erp__table">
                        <thead><tr><th>Name</th><th>Title</th><th>Open work</th><th>Status</th>@if ($mayChangeRoles)<th></th>@endif</tr></thead>
                        <tbody>
                            @foreach ($engineers as $person)
                                <tr>
                                    <td><b>{{ $person->name }}</b><br><span class="erp__muted">{{ $person->email }}</span></td>
                                    <td class="erp__muted">{{ $person->job_title }}</td>
                                    <td>{{ $person->open_tasks_count }}</td>
                                    <td>@if ($person->is_active)<span class="erp__chip erp__chip--positive">Active</span>@else<span class="erp__chip erp__chip--muted">Suspended</span>@endif</td>
                                    @if ($mayChangeRoles)
                                        <td class="erp__cellright">
                                            <form method="POST" action="{{ route('erp.people.update', $person) }}" class="erp__inlineform">
                                                @csrf @method('PUT')
                                                <select name="erp_role">
                                                    @foreach (ErpRole::cases() as $role)
                                                        <option value="{{ $role->value }}" @selected($person->erpRole() === $role)>{{ $role->label() }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="job_title" value="{{ $person->job_title }}">
                                                <select name="is_active">
                                                    <option value="1" @selected($person->is_active)>Active</option>
                                                    <option value="0" @selected(! $person->is_active)>Suspended</option>
                                                </select>
                                                <button type="submit" class="erp__btn erp__btn--sm erp__btn--ghost">Save</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </section>
        </div>

        <aside>
            <section class="erp__panel">
                <h2>Enrol someone</h2>
                <p class="erp__sub erp__muted">
                    As {{ $actor->erpRole()->label() }} you may enrol:
                    {{ collect($creatable)->map->label()->join(', ', ' and ') }}.
                </p>

                <form method="POST" action="{{ route('erp.people.store') }}" class="erp__form">
                    @csrf
                    <label for="erp_role">Post</label>
                    <select id="erp_role" name="erp_role" required>
                        @foreach ($creatable as $role)
                            <option value="{{ $role->value }}" @selected(old('erp_role') === $role->value)>{{ $role->label() }}</option>
                        @endforeach
                    </select>

                    <label for="name">Name</label>
                    <input id="name" name="name" required value="{{ old('name') }}">

                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                           placeholder="first.last@jiranisokotech.co.ke">

                    <label for="job_title">Title</label>
                    <input id="job_title" name="job_title" list="disciplines" value="{{ old('job_title') }}"
                           placeholder="Leave empty to use the post name">
                    <datalist id="disciplines">
                        @foreach ($disciplines as $d)<option value="{{ $d }}">@endforeach
                    </datalist>

                    <label for="password">First password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password">
                    <label for="password_confirmation">Confirm</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

                    <button type="submit" class="erp__btn">Enrol</button>
                </form>

                <p class="erp__muted" style="margin-top:10px">
                    Twelve characters minimum, with letters, numbers and symbols. Hand it over
                    directly and have them change it.
                </p>
            </section>

            <section class="erp__panel">
                <h2>What each post may do</h2>
                <dl class="erp__dl" style="grid-template-columns: 1fr">
                    @foreach (ErpRole::cases() as $role)
                        <dt style="color: var(--ink); font-weight: 700">{{ $role->label() }}</dt>
                        <dd class="erp__muted" style="margin-bottom: 8px">{{ $role->descriptor() }}</dd>
                    @endforeach
                </dl>
            </section>
        </aside>
    </div>
</x-layouts.erp>
