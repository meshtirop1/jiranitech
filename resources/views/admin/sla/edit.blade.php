<x-layouts.admin
    title="Service levels"
    subtitle="Every figure here is something a client can hold us to. They are quoted on the homepage, the delivery model page and every service page, and clause 6 of the terms of engagement says the binding numbers are the ones written into a statement of work."
>
    <form method="POST" action="{{ route('admin.sla.update') }}">
        @csrf
        @method('PUT')

        @foreach ($tiers as $tier)
            <div class="adm__panel">
                <div class="adm__panelhead">
                    <div>
                        <h2>{{ $tier->label() }}</h2>
                        <p class="adm__meta">{{ $tier->value }}</p>
                    </div>
                    <span class="pill {{ $tier->hasServiceCredits() ? 'pill--closed' : 'pill--open' }}">
                        {{ $tier->hasServiceCredits() ? 'Service credits' : 'No credits' }}
                    </span>
                </div>

                <div class="adm__row">
                    <div class="adm__grid">
                        @foreach ($fields as $field => $label)
                            @php($key = 'sla_'.$tier->value.'_'.$field)
                            <div class="adm__field">
                                <label for="{{ $key }}">{{ $label }}</label>
                                <input type="text" id="{{ $key }}" name="{{ $key }}"
                                       value="{{ old($key, config('sla.tiers.'.$tier->value.'.'.$field)) }}" required>
                                @if ($field === 'availability_target')
                                    <small>Must be achievable on the composite service level of everything underneath it.</small>
                                @endif
                            </div>
                        @endforeach

                        @php($creditsKey = 'sla_'.$tier->value.'_service_credits')
                        <label class="adm__toggle adm__field--wide" for="{{ $creditsKey }}">
                            <input type="checkbox" id="{{ $creditsKey }}" name="{{ $creditsKey }}" value="1"
                                   @checked(old($creditsKey, $tier->hasServiceCredits()))>
                            <span>
                                <b>This tier carries service credits</b>
                                <em>Capped at {{ config('legal.terms.service_credit_cap_percent') }}% of the monthly service charge by clause 6.4 of the terms.</em>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="adm__panel {{ $ratified ? '' : 'adm__panel--attention' }}">
            <div class="adm__panelhead">
                <div>
                    <h2>Gate G-05 — ratification</h2>
                    <p class="adm__meta">
                        {{ $ratified ? 'Ratified on '.\App\Enums\SlaTier::ratifiedOn() : 'Not ratified' }}
                    </p>
                </div>
            </div>

            <div class="adm__row">
                <div class="adm__grid">
                    <label class="adm__toggle adm__field--wide" for="ratified">
                        <input type="checkbox" id="ratified" name="ratified" value="1" @checked(old('ratified', $ratified))>
                        <span>
                            <b>These targets are achievable and contractually bound</b>
                            <em>
                                Tick this only once somebody has checked each availability target against the
                                composite service level of the infrastructure it runs on, and the commercial
                                terms behind the credits are agreed. Until then the site describes the tiers as
                                a framework rather than quoting them as commitments, which is the honest
                                position and not a defect.
                            </em>
                        </span>
                    </label>

                    <p class="adm__meta adm__field--wide">
                        Changing any figure above clears an existing ratification. The numbers that were
                        confirmed would no longer be the numbers published.
                    </p>
                </div>

                <div class="adm__actions">
                    <button type="submit" class="btn btn--primary btn--sm">Save service levels</button>
                </div>
            </div>
        </div>
    </form>
</x-layouts.admin>
