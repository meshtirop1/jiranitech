<x-layouts.app
    title="Delivery Model"
    description="How a Jiranisoko Tech Solutions engagement runs — from scoping through architecture, build, security review and documented handover — and the SLA framework governing it."
>
    <x-site.page-header
        eyebrow="Methodology and service framework"
        heading="How we deliver."
        standfirst="The same sequence on every engagement, whatever the contracting structure. It exists so that a client always knows what is happening now, what happens next, and what they will hold at the end."
        :crumbs="['Company' => route('company.index'), 'Delivery model' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell stack-lg">
            <div>
                <h2 class="display-3" style="margin-bottom:1rem">Engagement sequence</h2>
                <p class="lede measure" style="margin-bottom:1.25rem">
                    Numbered because the order carries information: each stage depends on the output of the one
                    before it, and we do not begin a stage whose input is not yet agreed.
                </p>
                <div class="deflist">
                    <div class="deflist__row">
                        <dt>01 — Scoping</dt>
                        <dd>A technical conversation, not a sales call. We establish the constraint that actually matters, the systems in play, and whether we are the right firm. Where we are not, we say so at this stage.</dd>
                    </div>
                    <div class="deflist__row">
                        <dt>02 — Architecture</dt>
                        <dd>Target architecture, integration boundaries, data model, security scope and non-functional requirements, issued as a written architecture decision record with the options we rejected and why.</dd>
                    </div>
                    <div class="deflist__row">
                        <dt>03 — Baseline</dt>
                        <dd>Scope baseline, delivery sequence, acceptance criteria and the service tier. This is the document the commercial terms attach to.</dd>
                    </div>
                    <div class="deflist__row">
                        <dt>04 — Build</dt>
                        <dd>Iterative delivery against the baseline, with automated testing, peer review, and security scanning enforced in the pipeline. Weekly delivery reporting; variations quoted and approved in writing.</dd>
                    </div>
                    <div class="deflist__row">
                        <dt>05 — Security review</dt>
                        <dd>Independent review against the applicable standard before any production exposure. Findings are remediated and retested, not accepted with a note.</dd>
                    </div>
                    <div class="deflist__row">
                        <dt>06 — Cutover</dt>
                        <dd>Rehearsed in a staging environment end to end, with a tested rollback path, before any production window is scheduled.</dd>
                    </div>
                    <div class="deflist__row">
                        <dt>07 — Operate</dt>
                        <dd>SLA-governed operation with monthly service reporting and quarterly service review, where the tier includes it.</dd>
                    </div>
                    <div class="deflist__row">
                        <dt>08 — Handover</dt>
                        <dd>Architecture records, runbooks, source, credentials and a working session with the team who will hold it. Delivered whether or not we continue to operate the system.</dd>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="display-3" style="margin-bottom:1rem">Service level framework</h2>
                <x-site.sla-table />
            </div>

            <div class="btn-row">
                <a class="btn btn--primary" href="{{ route('rfp.create') }}">Start a scoping conversation</a>
                <a class="btn btn--secondary" href="{{ route('engagement-models.index') }}">Contracting structures</a>
            </div>
        </div>
    </section>
</x-layouts.app>
