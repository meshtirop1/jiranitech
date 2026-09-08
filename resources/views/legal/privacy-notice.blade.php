@php
    $controller = config('company.parent.name');
    $registration = config('company.parent.registration_number');
    $division = config('company.legal_name');
    $privacy = config('company.email.privacy');
    $enquiries = config('company.email.enquiries');
    $careers = config('company.email.careers');
    $postal = config('company.postal_address');
    $city = config('company.city');
    $country = config('company.country');
    $version = config('legal.privacy.version');
    $effective = config('legal.privacy.effective_on');
@endphp

<x-layouts.app
    title="Privacy Notice"
    description="How Jiranisoko Tech Solutions collects, uses and protects personal data, and the rights you have over it."
>
    <x-site.page-header
        eyebrow="Legal"
        heading="Privacy notice"
        :crumbs="['Legal' => null, 'Privacy notice' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell">
            <div style="max-width:820px" class="stack-lg">

                <div class="stack">
                    <p class="lede measure">
                        This notice explains what personal data we collect when you use this website or deal
                        with us, why we hold it, how long we keep it and what you can require of us. It covers
                        data for which we are the controller. Where we process personal data on a client's
                        instructions we are a processor, and the
                        <a class="textlink" href="{{ route('legal.data-processing') }}">data processing addendum</a>
                        governs that instead.
                    </p>

                    <div class="deflist">
                        <div class="deflist__row">
                            <dt>Controller</dt>
                            <dd>
                                {{ $controller }}@if ($registration) ({{ $registration }})@endif, a company
                                incorporated in the Republic of Kenya, acting through its {{ $division }}
                                division.@if ($postal) Correspondence to {{ $postal }}, {{ $city }}, {{ $country }}.@endif
                            </dd>
                        </div>
                        @if ($privacy)
                            <div class="deflist__row">
                                <dt>Data protection contact</dt>
                                <dd>
                                    Our Data Protection Officer, at
                                    <a class="textlink" href="mailto:{{ $privacy }}">{{ $privacy }}</a>. This is a
                                    monitored role address, not an individual, so a leave or a departure never
                                    orphans a request.
                                </dd>
                            </div>
                        @endif
                        <div class="deflist__row">
                            <dt>Version</dt>
                            <dd>{{ $version }}, effective {{ \Illuminate\Support\Carbon::parse($effective)->format('j F Y') }}.</dd>
                        </div>
                    </div>
                </div>

                {{-- --------------------------------------------- what we hold --- --}}

                <div class="stack">
                    <h2 class="display-3">What we collect, and why</h2>
                    <p class="prose-body measure">
                        Everything below is something this site or this business actually does. We have not
                        listed categories we do not collect in order to look thorough.
                    </p>

                    <div class="tablewrap">
                        <table class="data">
                            <thead>
                                <tr>
                                    <th scope="col">What</th>
                                    <th scope="col">Why we hold it</th>
                                    <th scope="col">Lawful basis</th>
                                    <th scope="col">How long</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Request for proposal</strong><br>
                                        Organisation, your name and role, business email, telephone, country,
                                        indicative budget band, timeline, a description of the scope, the
                                        disciplines you are interested in, and whether you want an NDA first.
                                    </td>
                                    <td>To answer your enquiry, prepare a proposal and run the engagement that may follow.</td>
                                    <td>Steps taken at your request before entering a contract; and our legitimate interest in responding to business enquiries.</td>
                                    <td>Twenty-four months from the last contact, unless it becomes an engagement, in which case it is kept for the life of the engagement and seven years after it, for tax and limitation purposes.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Submission metadata</strong><br>
                                        The IP address the submission came from and the page you came from.
                                    </td>
                                    <td>To detect and investigate abuse of the form, and to evidence when and from where a submission arrived if it is ever disputed.</td>
                                    <td>Our legitimate interest in the security and integrity of our systems.</td>
                                    <td>Deleted with the submission it belongs to.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Correspondence</strong><br>
                                        Whatever you put in an email to one of our role addresses, and our reply.
                                    </td>
                                    <td>To deal with what you wrote to us about and keep a record of what was agreed.</td>
                                    <td>Performance of a contract, or our legitimate interest in handling correspondence.</td>
                                    <td>Twenty-four months from the end of the exchange, or seven years where it records something contractual.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Job applications</strong><br>
                                        Your CV and anything you send with it, sent to {{ $careers ?: config('company.email.enquiries') }}.
                                    </td>
                                    <td>To assess you for the role you applied for.</td>
                                    <td>Steps taken at your request before entering a contract of employment.</td>
                                    <td>Six months after the role is filled or withdrawn. Longer only if you ask us to keep you on file, which we will confirm in writing.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Session cookies</strong><br>
                                        A session cookie and a cross-site request forgery token.
                                    </td>
                                    <td>To make the forms on this site work and to protect them from being submitted from another site in your name.</td>
                                    <td>Strictly necessary. No consent banner is shown because there is nothing to consent to.</td>
                                    <td>The browser session, or two hours of inactivity, whichever ends first.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Server logs</strong><br>
                                        Requests to the site, with IP address, time, page and browser.
                                    </td>
                                    <td>To keep the service running and to investigate faults and attacks.</td>
                                    <td>Our legitimate interest in operating and securing the service.</td>
                                    <td>As configured by our hosting provider. We do not mine these logs for any purpose beyond operations and security.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- -------------------------------------------- what we don't --- --}}

                <div class="stack">
                    <h2 class="display-3">What this site does not do</h2>
                    <p class="prose-body measure">
                        These are worth stating because they are unusual, and because they are the reason this
                        page is short.
                    </p>
                    <ul class="cell__list">
                        <li style="font-size:.92rem;color:var(--ink-2)">No analytics. We do not run Google Analytics or any equivalent, and we do not know how many people read any given page.</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">No advertising, no remarketing, no tracking pixels, no social media embeds.</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">No third-party fonts or content delivery network. Every font, image and script is served from this domain, so your IP address is not disclosed to a third party by loading a page here.</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">No profiling and no automated decision-making that produces legal or similarly significant effects.</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">We do not sell personal data, and we do not share it for anyone else's marketing.</li>
                    </ul>
                </div>

                {{-- --------------------------------------------- who else sees --- --}}

                <div class="stack">
                    <h2 class="display-3">Who else sees it</h2>
                    <p class="prose-body measure">
                        Personal data you give us is seen by the people in this firm who need it for the purpose
                        it was collected for, and by the service providers that run our infrastructure. Those
                        providers are named in the
                        <a class="textlink" href="{{ route('legal.sub-processors') }}">sub-processor register</a>,
                        which states each one's role, where it processes data and the safeguard relied upon.
                    </p>
                    <p class="prose-body measure">
                        We will also disclose personal data where we are legally required to — to a court, a
                        regulator, or the Office of the Data Protection Commissioner. Where we are permitted to
                        tell you that we have done so, we will.
                    </p>
                </div>

                {{-- ------------------------------------------------- transfers --- --}}

                <div class="stack">
                    <h2 class="display-3">Where it is processed</h2>
                    <p class="prose-body measure">
                        Our infrastructure is provided by a Kenyan company and we do not transfer personal data
                        out of Kenya for our own purposes. Where any processing does take place outside Kenya,
                        the sub-processor register states it and the safeguard relied upon, which will be an
                        adequacy finding, appropriate safeguards recognised by the Kenya Data Protection Act
                        2019, or standard contractual clauses where the GDPR also applies.
                    </p>
                </div>

                {{-- ---------------------------------------------------- rights --- --}}

                <div class="stack">
                    <h2 class="display-3">Your rights</h2>
                    <p class="prose-body measure">
                        Under the Kenya Data Protection Act 2019, and under the GDPR where it applies to you,
                        you may:
                    </p>
                    <div class="deflist">
                        <div class="deflist__row"><dt>Be informed</dt><dd>Know how your personal data is being used. That is what this notice is for.</dd></div>
                        <div class="deflist__row"><dt>Access</dt><dd>Obtain a copy of the personal data we hold about you.</dd></div>
                        <div class="deflist__row"><dt>Correct</dt><dd>Have inaccurate or incomplete data corrected or completed.</dd></div>
                        <div class="deflist__row"><dt>Delete</dt><dd>Have data erased where we no longer have a good reason to hold it.</dd></div>
                        <div class="deflist__row"><dt>Object and restrict</dt><dd>Object to processing we carry out on the basis of legitimate interests, and ask us to restrict processing while an objection or a correction is being resolved.</dd></div>
                        <div class="deflist__row"><dt>Portability</dt><dd>Receive data you gave us in a structured, commonly used, machine-readable format, and have it sent to another controller where that is technically feasible.</dd></div>
                        <div class="deflist__row"><dt>Withdraw consent</dt><dd>Where we rely on consent, withdraw it at any time. That does not affect processing already carried out.</dd></div>
                    </div>

                    <p class="prose-body measure">
                        @if ($privacy)
                            To exercise any of these, write to
                            <a class="textlink" href="mailto:{{ $privacy }}">{{ $privacy }}</a>.
                        @elseif ($enquiries)
                            To exercise any of these, write to
                            <a class="textlink" href="mailto:{{ $enquiries }}">{{ $enquiries }}</a>.
                        @endif
                        We will acknowledge within {{ config('company.response.acknowledgement') }} and respond
                        substantively within thirty days. If a request is complex we may extend that, and we
                        will tell you why before the thirty days are up rather than after. We do not charge for
                        this. We may ask you to verify your identity, and we will ask for no more than is
                        necessary to do so.
                    </p>

                    <p class="prose-body measure">
                        If you are not satisfied with how we have handled your data or your request, you may
                        complain to the Office of the Data Protection Commissioner in Kenya, or to the
                        supervisory authority where you live if the GDPR applies to you. We would rather you
                        raised it with us first, but you are not obliged to.
                    </p>
                </div>

                {{-- -------------------------------------------------- security --- --}}

                <div class="stack">
                    <h2 class="display-3">How it is protected</h2>
                    <p class="prose-body measure">
                        Connections to this site are encrypted in transit. Access to submitted data is limited
                        to the people who need it, granted by named post rather than shared account, and
                        withdrawn when someone's role changes. Passwords to our systems are stored only as
                        one-way hashes. The specific technical and organisational measures we operate are listed
                        in Annex 2 of the
                        <a class="textlink" href="{{ route('legal.data-processing') }}">data processing addendum</a>,
                        including the measures we do not yet operate, because a controller assessing us is
                        better served by an accurate list than a flattering one.
                    </p>
                    <p class="prose-body measure">
                        If a breach affects your personal data and is likely to result in a risk to your rights
                        and freedoms, we will notify the Data Commissioner within seventy-two hours of becoming
                        aware of it, and tell you without undue delay where the risk to you is high.
                    </p>
                </div>

                {{-- --------------------------------------------------- changes --- --}}

                <div class="stack">
                    <h2 class="display-3">Changes to this notice</h2>
                    <p class="prose-body measure">
                        This notice carries a version number and an effective date, both shown at the top. When
                        we change it materially we will say what changed and when it takes effect, and where the
                        change affects data we already hold on the basis of your consent, we will ask again
                        rather than assume.
                    </p>
                </div>

            </div>
        </div>
    </section>
</x-layouts.app>
