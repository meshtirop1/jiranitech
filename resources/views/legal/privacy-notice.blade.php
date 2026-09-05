<x-layouts.app title="Privacy Notice" description="How Jiranisoko Tech Solutions handles personal data collected through this website.">
    <x-site.page-header
        eyebrow="Legal"
        heading="Privacy notice"
        :crumbs="['Legal' => null, 'Privacy notice' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell">
            <div style="max-width:820px" class="stack-lg">
                <div class="note note--gate">
                    <span class="note__tag">Publication gate G-06 — counsel required</span>
                    <p>
                        This notice has not been drafted. A privacy notice is a legal instrument with statutory
                        content requirements under the Kenya Data Protection Act 2019 and, where EU or UK data
                        subjects are involved, the GDPR. It must be written or reviewed by counsel and signed
                        off by the data protection officer. The structure below records what this site actually
                        collects, so that whoever drafts it is working from fact rather than a template.
                    </p>
                </div>

                <div class="stack">
                    <h2 class="display-3">What this site actually collects — for the drafter</h2>
                    <div class="deflist">
                        <div class="deflist__row">
                            <dt>RFP submissions</dt>
                            <dd>Organisation, contact name, role, business email, telephone, country, indicative budget, timeline, scope description, disciplines of interest, NDA preference. Stored in the application database. Submitter IP address and referring page are recorded alongside the submission.</dd>
                        </div>
                        <div class="deflist__row">
                            <dt>Session cookies</dt>
                            <dd>A Laravel session cookie and a CSRF token cookie, both strictly necessary for the form to function. No analytics, advertising or third-party tracking cookies are set by this application as built.</dd>
                        </div>
                        <div class="deflist__row">
                            <dt>Server logs</dt>
                            <dd>Standard web server request logs, retention governed by the hosting configuration.</dd>
                        </div>
                        <div class="deflist__row">
                            <dt>Fonts and assets</dt>
                            <dd>All fonts and assets are served from this domain. No visitor IP address is disclosed to a third-party font or CDN provider by the application as built.</dd>
                        </div>
                    </div>
                </div>

                <div class="stack">
                    <h2 class="display-3">Sections the drafted notice must contain</h2>
                    <ul class="cell__list">
                        <li style="font-size:.92rem;color:var(--ink-2)">Identity and contact details of the data controller, and of the data protection officer</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">Categories of personal data collected and the purpose of each</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">Lawful basis for each processing purpose</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">Recipients and any sub-processors, including hosting</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">Any transfer outside Kenya and the safeguard relied upon</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">Retention period for each category, or the criteria determining it</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">Data subject rights and how to exercise them</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">Right to lodge a complaint with the Office of the Data Protection Commissioner</li>
                        <li style="font-size:.92rem;color:var(--ink-2)">Date of the notice and how changes are communicated</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
