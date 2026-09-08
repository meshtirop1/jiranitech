<x-layouts.erp title="People">
    <div class="erp__head">
        <div><h1>People</h1>
            <p class="erp__sub">Everyone with a delivery role. A site administrator is not automatically an engineer.</p></div>
        <div class="erp__stats"><span><b>{{ $people->count() }}</b> in delivery</span></div>
    </div>

    <div class="erp__split">
        <div>
            <section class="erp__panel">
                <h2>Delivery accounts</h2>
                <table class="erp__table">
                    <thead><tr><th>Name</th><th>Role</th><th>Open work</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($people as $person)
                            <tr>
                                <td><b>{{ $person->name }}</b><br><span class="erp__muted">{{ $person->email }}</span></td>
                                <td>{{ $person->erpRole()?->label() }}<br><span class="erp__muted">{{ $person->job_title }}</span></td>
                                <td>{{ $person->open_tasks_count }}</td>
                                <td>@if ($person->is_active)<span class="erp__chip erp__chip--positive">Active</span>@else<span class="erp__chip erp__chip--muted">Suspended</span>@endif</td>
                                <td class="erp__cellright">
                                    <form method="POST" action="{{ route('erp.people.update', $person) }}" class="erp__inlineform">
                                        @csrf @method('PUT')
                                        <select name="erp_role">
                                            @foreach ($roles as $role)
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        </div>

        <aside>
            <section class="erp__panel">
                <h2>Enrol someone</h2>
                <form method="POST" action="{{ route('erp.people.store') }}" class="erp__form">
                    @csrf
                    <label for="name">Name</label>
                    <input id="name" name="name" required value="{{ old('name') }}">

                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}">

                    <label for="job_title">Job title</label>
                    <input id="job_title" name="job_title" value="{{ old('job_title') }}" placeholder="Senior engineer">

                    <label for="erp_role">Role</label>
                    <select id="erp_role" name="erp_role" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                        @endforeach
                    </select>

                    <label for="password">First password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password">
                    <label for="password_confirmation">Confirm</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

                    <button type="submit" class="erp__btn">Create account</button>
                </form>
                <p class="erp__muted">Give the password to them directly and have them change it. Twelve characters minimum, with letters, numbers and symbols.</p>
            </section>
        </aside>
    </div>
</x-layouts.erp>
