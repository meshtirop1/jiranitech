<x-layouts.admin
    title="Site settings"
    subtitle="Corporate identity and the two gates that are a judgement call rather than a data state. These are stored in the database, not in .env, so they can be changed from here on a host with no shell."
>
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="adm__panel">
            <div class="adm__panelhead">
                <div>
                    <h2>Corporate identity</h2>
                    <p>
                        Gate G-07. Registered office, company registration number and a contact route
                        are mandatory before launch — the footer shows a marker on every page until
                        they are set, and their absence is a documented disqualifier in vendor screening.
                    </p>
                </div>
            </div>

            <div class="adm__row">
                <div class="adm__grid">
                    @foreach ($fields as $key => [$label, $hint, $type])
                        <div class="adm__field @error($key) adm__field--invalid @enderror">
                            <label for="{{ $key }}">{{ $label }}</label>
                            <input
                                type="{{ $type === 'email' ? 'email' : ($type === 'url' ? 'url' : 'text') }}"
                                id="{{ $key }}"
                                name="{{ $key }}"
                                value="{{ old($key, $values[$key] ?? '') }}"
                            >
                            @if ($hint)
                                <small>{{ $hint }}</small>
                            @endif
                            @error($key)
                                <span class="adm__err">{{ $message }}</span>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="adm__panel">
            <div class="adm__panelhead">
                <div>
                    <h2>Sign-off gates</h2>
                    <p>
                        These two are not data the site can check for itself — they are confirmations
                        that a person has done something. Only tick them once it is actually true.
                    </p>
                </div>
            </div>

            <div class="adm__row">
                <div class="adm__grid">
                    <p class="adm__meta adm__field--wide">
                        G-05, the ratification of the service level targets, is decided alongside the figures
                        themselves on <a class="textlink" href="{{ route('admin.sla.edit') }}">Service levels</a>.
                    </p>

                    <label class="adm__toggle adm__field--wide">
                        <input type="checkbox" name="gate_g06_counsel_signed_off" value="1" @checked(old('gate_g06_counsel_signed_off', $gateG06))>
                        <span>
                            <b>G-06 — Data protection instruments signed off</b>
                            <small>
                                Counsel has drafted the Data Processing Addendum and Privacy Notice, and the
                                sub-processor register is published. Several pages already state that a DPA
                                governs processing; until this is true, that claim is unsupported.
                            </small>
                        </span>
                    </label>
                </div>

                <div class="adm__actions">
                    <button type="submit" class="btn btn--primary">Save settings</button>
                    <a class="btn btn--ghost" href="{{ route('admin.dashboard') }}">Back to dashboard</a>
                </div>
            </div>
        </div>
    </form>
</x-layouts.admin>
