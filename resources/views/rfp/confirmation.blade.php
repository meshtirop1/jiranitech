{{-- Template T-14 — intake confirmation. --}}

{{-- Reached only through a signed, single-use URL. It names the sender's
     organisation and their reference, so it is kept out of every index. --}}
<x-layouts.app title="Request received" :noindex="true">
    <x-site.page-header
        eyebrow="Request received"
        :heading="'Your reference is '.$submission->reference"
        standfirst="We have your enquiry. What happens next is described below — no automated nurture sequence, no sales cadence."
    />

    <section class="section" style="padding-top:2rem">
        <div class="shell">
            <div style="max-width:820px" class="stack-lg">
                <div class="deflist">
                    <div class="deflist__row"><dt>Reference</dt><dd class="mono">{{ $submission->reference }}</dd></div>
                    <div class="deflist__row"><dt>Track</dt><dd>{{ $submission->track->label() }}</dd></div>
                    <div class="deflist__row"><dt>Organisation</dt><dd>{{ $submission->organisation }}</dd></div>
                    <div class="deflist__row"><dt>Reply address</dt><dd>{{ $submission->email }}</dd></div>
                    <div class="deflist__row"><dt>Received</dt><dd>{{ $submission->created_at->format('j F Y, H:i') }} {{ config('company.timezone_label') }}</dd></div>
                    @if ($submission->nda_required)
                        <div class="deflist__row"><dt>NDA</dt><dd>A mutual non-disclosure agreement will be sent for signature before we ask any further questions about scope.</dd></div>
                    @endif
                </div>

                <div>
                    <h2 class="eyebrow" style="margin-bottom:1rem">What happens next</h2>
                    <div class="deflist">
                        <div class="deflist__row">
                            <dt>Within {{ config('company.response.acknowledgement') }}</dt>
                            <dd>An acknowledgement from a named person, not an autoresponder.</dd>
                        </div>
                        <div class="deflist__row">
                            <dt>Within {{ config('company.response.substantive') }}</dt>
                            <dd>A substantive reply from the engineering lead who owns the {{ $submission->track->label() }} practice, with either an initial technical view or the specific questions we need answered to form one.</dd>
                        </div>
                        <div class="deflist__row">
                            <dt>Thereafter</dt>
                            <dd>A scoping session at your convenience. We do not issue a proposal before we understand the constraint, because a proposal written without one is a guess with a price on it.</dd>
                        </div>
                    </div>
                </div>

                <p class="small muted">
                    Quote reference <strong class="mono">{{ $submission->reference }}</strong> in any correspondence.
                    This page is a private link and expires after six hours; the reference does not.
                </p>

                <div class="btn-row">
                    <a class="btn btn--secondary" href="{{ route('home') }}">Return to the homepage</a>
                    <a class="btn btn--ghost" href="{{ route('insights.index') }}">Read our engineering notes</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
