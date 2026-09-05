{{-- Template T-05 — section index. --}}

<x-layouts.app
    title="Engagement Models"
    description="Three contracting structures — turnkey solution delivery, dedicated engineering teams, and enterprise strategic advisory — with the commercial basis and scope boundary of each stated in full."
>
    <x-site.page-header
        eyebrow="How we are engaged"
        heading="Three contracting structures. Chosen by your risk position, not by our preference."
        standfirst="Where the scope is definable and the outcome is fixed, we carry the delivery risk. Where the scope will evolve and your team must own the result, we embed senior engineers inside your organisation. Where the decision precedes the build, we advise and step back."
        :crumbs="['Engagement Models' => null]"
    />

    <section class="section">
        <div class="shell stack-lg">
            @foreach ($engagementModels as $model)
                <article class="stack" id="{{ $model->slug }}">
                    <p class="eyebrow">{{ $model->reference }}</p>
                    <h2 class="display-3">
                        <a href="{{ route('engagement-models.show', $model) }}" style="text-decoration:none;color:inherit">{{ $model->title }}</a>
                    </h2>
                    <p class="prose-body measure">{{ $model->summary }}</p>

                    <div class="deflist" style="margin-top:.5rem">
                        <div class="deflist__row"><dt>Commercial basis</dt><dd>{{ $model->commercial_basis }}</dd></div>
                        <div class="deflist__row"><dt>Scope boundary</dt><dd>{{ $model->scope_boundary }}</dd></div>
                        <div class="deflist__row"><dt>Governance cadence</dt><dd>{{ $model->governance_cadence }}</dd></div>
                        <div class="deflist__row"><dt>Suited to</dt><dd>{{ $model->suited_to }}</dd></div>
                    </div>

                    <div class="btn-row" style="margin-top:.5rem">
                        <a class="btn btn--secondary" href="{{ route('rfp.create', ['track' => $model->rfp_track->value]) }}">
                            Start an enquiry on this basis
                        </a>
                    </div>
                </article>
            @endforeach

            <div class="note">
                <span class="note__tag">On choosing between them</span>
                <p>
                    We do not mark one of these as recommended. Signalling a preferred model would undermine
                    the point of publishing all three: the right structure follows from where the delivery risk
                    should sit, which is a question about your organisation rather than about ours.
                </p>
            </div>
        </div>
    </section>

    <x-site.rfp-band />
</x-layouts.app>
