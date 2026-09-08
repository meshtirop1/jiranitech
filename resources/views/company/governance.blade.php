{{-- Template T-11 — governance and compliance register. --}}

<x-layouts.app
    title="Governance"
    description="Security posture, compliance register, service level framework and data protection obligations governing every Jiranisoko Tech Solutions engagement."
>
    <x-site.page-header
        eyebrow="Governance, compliance and service assurance"
        heading="Contractual commitments, not aspirations."
        standfirst="Every engagement is governed by a written service framework. These terms are available for review before contract, and our security documentation is released to prospective clients under non-disclosure agreement on request."
        :crumbs="['Company' => route('company.index'), 'Governance' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell stack-lg">
            <div>
                <h2 class="display-3" style="margin-bottom:1rem">Compliance register</h2>
                <p class="lede measure" style="margin-bottom:1.25rem">
                    Each standard we work to, with its current status. A standard we follow but have not
                    certified is shown as aligned rather than certified, because the distinction is material
                    to a vendor risk reviewer and misrepresenting it is a disqualifying event.
                </p>

                <div class="tablewrap">
                    <table class="data">
                        <thead>
                            <tr>
                                <th scope="col">Standard</th>
                                <th scope="col">Status</th>
                                <th scope="col">Last reviewed</th>
                                <th scope="col">Evidence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($claims as $claim)
                                <tr>
                                    <th scope="row" style="font-weight:700">{{ $claim->standard }}</th>
                                    <td>{{ $claim->status->label() }}</td>
                                    <td class="num">{{ $claim->reviewed_on->format('M Y') }}</td>
                                    <td>
                                        @if ($claim->evidence_url)
                                            <a href="{{ $claim->evidence_url }}">Certificate</a>
                                        @elseif ($claim->status->isAssertable())
                                            <span class="pending">Certificate reference missing</span>
                                        @else
                                            <span class="muted">Available under NDA</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>

            <div>
                <h2 class="display-3" style="margin-bottom:1rem">Service level framework</h2>
                <x-site.sla-table />
            </div>

            <div class="hairline-grid hairline-grid--2">
                <div class="cell">
                    <span class="cell__marker">Security</span>
                    <h3 class="cell__title">Controls</h3>
                    <ul class="cell__list" style="margin-top:.5rem">
                        <li style="font-size:.9rem;color:var(--ink-2)">Role-based access with least privilege and quarterly access review</li>
                        <li style="font-size:.9rem;color:var(--ink-2)">Mandatory peer code review before merge</li>
                        <li style="font-size:.9rem;color:var(--ink-2)">Dependency, secret and container scanning enforced in CI, failing closed on critical findings</li>
                        <li style="font-size:.9rem;color:var(--ink-2)">Annual third-party penetration testing with remediation tracked to closure</li>
                        <li style="font-size:.9rem;color:var(--ink-2)">Published responsible disclosure channel</li>
                    </ul>
                </div>
                <div class="cell">
                    <span class="cell__marker">Data protection</span>
                    <h3 class="cell__title">Processing obligations</h3>
                    <ul class="cell__list" style="margin-top:.5rem">
                        <li style="font-size:.9rem;color:var(--ink-2)">Data Processing Addendum meeting Kenya Data Protection Act 2019 and GDPR Article 28</li>
                        <li style="font-size:.9rem;color:var(--ink-2)">Region-selectable data residency, agreed before any data is processed</li>
                        <li style="font-size:.9rem;color:var(--ink-2)">Sub-processor register with change notification</li>
                        <li style="font-size:.9rem;color:var(--ink-2)">Documented retention and deletion schedule per engagement</li>
                        <li style="font-size:.9rem;color:var(--ink-2)">Breach notification within the periods the applicable law requires</li>
                    </ul>
                </div>
            </div>

            <div class="note">
                <span class="note__tag">The instruments themselves</span>
                <p>
                    These obligations are not a summary of intent. They are set out in our
                    <a class="textlink" href="{{ route('legal.data-processing') }}">data processing addendum</a>,
                    which attaches to every engagement that involves personal data, and the
                    <a class="textlink" href="{{ route('legal.sub-processors') }}">sub-processor register</a>
                    it is honoured against. Both are published in full rather than sent on request.
                </p>
            </div>

            <div class="btn-row">
                <a class="btn btn--primary" href="{{ route('rfp.create', ['track' => \App\Enums\RfpTrack::StrategicAdvisory->value]) }}">Request our security pack</a>
                <a class="btn btn--secondary" href="{{ route('legal.disclosure') }}">Responsible disclosure policy</a>
            </div>
        </div>
    </section>
</x-layouts.app>
