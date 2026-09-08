@php
    $notice = config('sub_processors.notice_days');
    $version = config('sub_processors.register_version');
    $effective = config('sub_processors.register_effective_on');
    $engaged = config('sub_processors.engaged', []);
    $conditional = config('sub_processors.conditional', []);
    $changes = config('sub_processors.changes', []);
    $privacy = config('company.email.privacy');
@endphp

<x-layouts.app
    title="Sub-processor Register"
    description="Every party that processes client personal data on behalf of Jiranisoko Tech Solutions, with its role, location and transfer safeguard."
>
    <x-site.page-header
        eyebrow="Legal"
        heading="Sub-processor register"
        :crumbs="['Legal' => null, 'Sub-processor register' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell">
            <div style="max-width:820px" class="stack-lg">

                <div class="stack">
                    <p class="lede measure">
                        This is Annex 3 to the
                        <a class="textlink" href="{{ route('legal.data-processing') }}">data processing addendum</a>.
                        It lists every party that processes client personal data on our behalf. A controller who
                        has signed the addendum is authorising the engaged list by name, and is entitled to
                        <strong>{{ $notice }} days'</strong> notice before anything is added to it.
                    </p>

                    <div class="deflist">
                        <div class="deflist__row">
                            <dt>Register version</dt>
                            <dd>{{ $version }}, effective {{ \Illuminate\Support\Carbon::parse($effective)->format('j F Y') }}. Quoted by version in Annex 1 of each engagement.</dd>
                        </div>
                        <div class="deflist__row">
                            <dt>Notice of change</dt>
                            <dd>Clause 7.4 of the addendum. Notice runs from the dated entry in the change log at the foot of this page.</dd>
                        </div>
                        <div class="deflist__row">
                            <dt>How to object</dt>
                            <dd>
                                On reasonable data protection grounds, in writing, within the notice period.
                                @if ($privacy)
                                    Objections go to <a class="textlink" href="mailto:{{ $privacy }}">{{ $privacy }}</a>.
                                @endif
                                Clause 7.5 sets out what follows.
                            </dd>
                        </div>
                    </div>
                </div>

                {{-- ------------------------------------------------- engaged --- --}}

                <div class="stack">
                    <h2 class="display-3">Engaged on every engagement</h2>
                    <p class="prose-body measure">
                        These process client personal data now. Signing the addendum authorises them.
                    </p>

                    @foreach ($engaged as $p)
                        <div class="tablewrap" style="margin-bottom:1.25rem">
                            <table class="data">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="2">{{ $p['name'] }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td style="width:11rem">Role</td><td>{{ $p['role'] }}</td></tr>
                                    <tr><td>Service provided</td><td>{{ $p['service'] }}</td></tr>
                                    <tr><td>Entity</td><td>{{ $p['entity'] }}</td></tr>
                                    <tr><td>Processing location</td><td>{{ $p['location'] }}</td></tr>
                                    <tr><td>How that is known</td><td>{{ $p['basis'] }}</td></tr>
                                    <tr><td>Transfer safeguard</td><td>{{ $p['transfer'] }}</td></tr>
                                    @if ($p['engaged_on'])
                                        <tr><td>Engaged since</td><td class="num">{{ \Illuminate\Support\Carbon::parse($p['engaged_on'])->format('j F Y') }}</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endforeach

                    @if (count($engaged) === 1)
                        <p class="prose-body measure">
                            One entry is not an omission. Our own web properties, the administration console and
                            the delivery system load no third-party analytics, advertising, content-delivery or
                            error-tracking service, and all fonts and assets are served from our own domain. The
                            register is short because the estate is.
                        </p>
                    @endif
                </div>

                {{-- --------------------------------------------- conditional --- --}}

                <div class="stack">
                    <h2 class="display-3">May be proposed for a specific engagement</h2>
                    <p class="prose-body measure">
                        Listing here is disclosure, not authorisation. None of these processes a controller's
                        personal data unless that controller authorises it in writing and the authorisation is
                        recorded in Annex 1 of their addendum.
                    </p>

                    @foreach ($conditional as $p)
                        <div class="tablewrap" style="margin-bottom:1.25rem">
                            <table class="data">
                                <thead>
                                    <tr><th scope="col" colspan="2">{{ $p['name'] }}</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td style="width:11rem">Role</td><td>{{ $p['role'] }}</td></tr>
                                    <tr><td>Service provided</td><td>{{ $p['service'] }}</td></tr>
                                    <tr><td>Entity</td><td>{{ $p['entity'] }}</td></tr>
                                    <tr><td>Processing location</td><td>{{ $p['location'] }}</td></tr>
                                    <tr><td>How that is known</td><td>{{ $p['basis'] }}</td></tr>
                                    <tr><td>Transfer safeguard</td><td>{{ $p['transfer'] }}</td></tr>
                                    <tr><td>Currently engaged</td><td>No. Authorisation is per engagement.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>

                {{-- -------------------------------------------------- changes --- --}}

                <div class="stack">
                    <h2 class="display-3">Change log</h2>
                    <p class="prose-body measure">
                        Every change to the lists above, newest first. This is the record the notice period in
                        clause 7.4 is measured against.
                    </p>

                    <div class="tablewrap">
                        <table class="data">
                            <thead>
                                <tr><th scope="col">Date</th><th scope="col">Change</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($changes as $change)
                                    <tr>
                                        <td class="num">{{ \Illuminate\Support\Carbon::parse($change['on'])->format('j M Y') }}</td>
                                        <td>{{ $change['note'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>
</x-layouts.app>
