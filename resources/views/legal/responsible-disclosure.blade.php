<x-layouts.app
    title="Responsible Disclosure"
    description="How to report a security vulnerability in a system operated by Jiranisoko Tech Solutions, and what we commit to in return."
>
    <x-site.page-header
        eyebrow="Security"
        heading="Responsible disclosure policy"
        standfirst="If you have found a vulnerability in a system we operate, we want to hear about it, and we will not take action against you for telling us in good faith."
        :crumbs="['Legal' => null, 'Responsible disclosure' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell">
            <div style="max-width:820px" class="stack-lg">
                <div class="stack">
                    <h2 class="display-3">How to report</h2>
                    <p class="prose-body measure">
                        Send your report to
                        @if (config('company.email.security'))
                            <a href="mailto:{{ config('company.email.security') }}">{{ config('company.email.security') }}</a>
                        @else
                            <span class="pending">Gate G-07 — security address not configured</span>
                        @endif
                        with enough detail to reproduce the issue: the affected system, the steps taken, and the
                        impact you believe it has. Proof-of-concept code is welcome. Please do not include third
                        party personal data in the report.
                    </p>
                </div>

                <div class="stack">
                    <h2 class="display-3">What we commit to</h2>
                    <div class="deflist">
                        <div class="deflist__row"><dt>Acknowledgement</dt><dd>Within two business days of receipt.</dd></div>
                        <div class="deflist__row"><dt>Triage</dt><dd>An initial severity assessment and our intended course of action within ten business days.</dd></div>
                        <div class="deflist__row"><dt>Progress</dt><dd>Updates at least every fifteen business days until the issue is resolved or we explain why it will not be.</dd></div>
                        <div class="deflist__row"><dt>Credit</dt><dd>Public acknowledgement on request, once the issue is remediated.</dd></div>
                        <div class="deflist__row"><dt>Good faith</dt><dd>We will not pursue legal action against a researcher who follows this policy, and we will say so in writing if you ask.</dd></div>
                    </div>
                </div>

                <div class="stack">
                    <h2 class="display-3">Scope and boundaries</h2>
                    <p class="prose-body measure">
                        This policy covers systems operated by {{ config('company.legal_name') }}. It does not
                        extend to systems belonging to our clients: if you believe you have found an issue in a
                        system we built but do not operate, report it to us and we will route it to the operator
                        rather than act on it ourselves.
                    </p>
                    <h3 class="eyebrow" style="margin-top:.75rem">Please do not</h3>
                    <ul class="cell__list">
                        <li style="font-size:.92rem;color:var(--ink-2)">Access, modify or delete data belonging to anyone other than yourself</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">Degrade service availability, including through automated load or denial of service testing</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">Use social engineering, phishing or physical intrusion against our staff or premises</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">Disclose the issue publicly before we have had a reasonable opportunity to remediate it</li>
                    </ul>
                </div>

                <div class="note">
                    <span class="note__tag">On timelines</span>
                    <p>
                        We do not operate a bug bounty and make no offer of payment. The commitments above are
                        about responsiveness, which in our experience is what researchers actually want from a
                        disclosure process.
                    </p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
