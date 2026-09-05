{{-- Template T-06 — engagement model detail. --}}

<x-layouts.app :title="$engagementModel->title" :description="$engagementModel->summary">
    <x-site.page-header
        :eyebrow="$engagementModel->reference"
        :heading="$engagementModel->title"
        :standfirst="$engagementModel->summary"
        :crumbs="[
            'Engagement Models' => route('engagement-models.index'),
            $engagementModel->title => null,
        ]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell stack-lg">
            <div class="deflist">
                <div class="deflist__row"><dt>Commercial basis</dt><dd>{{ $engagementModel->commercial_basis }}</dd></div>
                <div class="deflist__row"><dt>Scope boundary</dt><dd>{{ $engagementModel->scope_boundary }}</dd></div>
                <div class="deflist__row"><dt>Governance cadence</dt><dd>{{ $engagementModel->governance_cadence }}</dd></div>
                <div class="deflist__row"><dt>Suited to</dt><dd>{{ $engagementModel->suited_to }}</dd></div>
                <div class="deflist__row"><dt>Intake track</dt><dd>{{ $engagementModel->rfp_track->label() }} — {{ $engagementModel->rfp_track->description() }}</dd></div>
            </div>

            <div class="btn-row">
                <a class="btn btn--primary" href="{{ route('rfp.create', ['track' => $engagementModel->rfp_track->value]) }}">
                    Submit an enquiry on this basis
                </a>
                <a class="btn btn--secondary" href="{{ route('company.delivery-model') }}">How we deliver</a>
            </div>
        </div>
    </section>

    <section class="section section--tinted">
        <div class="shell">
            <div class="section-head">
                <p class="eyebrow">The alternatives</p>
                <h2 class="display-3">If this is not the right structure</h2>
            </div>
            <div class="hairline-grid hairline-grid--2">
                @foreach ($siblings as $sibling)
                    <a class="cell" href="{{ route('engagement-models.show', $sibling) }}">
                        <span class="cell__marker">{{ $sibling->reference }}</span>
                        <h3 class="cell__title">{{ $sibling->title }}</h3>
                        <p class="cell__body">{{ $sibling->summary }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
