{{--
    H-11 — RFP conversion band.

    The only inverted section on a page, so it reads as the terminal action. Each route
    presets the track on the intake form, which then adapts its later questions.
--}}

@props([
    'heading' => 'Begin with a scoped conversation, not a sales call.',
])

@php
    $tracks = [
        \App\Enums\RfpTrack::EnterpriseModernisation,
        \App\Enums\RfpTrack::NewProductBuild,
        \App\Enums\RfpTrack::TeamAugmentation,
    ];
@endphp

<section class="band" aria-labelledby="rfp-band-heading">
    <div class="shell">
        <p class="eyebrow">Request for Proposal</p>
        <h2 class="display-2" id="rfp-band-heading" style="margin-top:.6rem;max-width:20ch">{{ $heading }}</h2>
        <p class="lede" style="margin-top:1rem;max-width:62ch;color:inherit;opacity:.86">
            Our intake is structured so that the first response you receive is technical. Tell us which
            of the following describes your position and we will route your enquiry to the engineering
            lead who owns that practice.
        </p>

        <div class="band__routes">
            @foreach ($tracks as $track)
                <a class="band__route" href="{{ route('rfp.create', ['track' => $track->value]) }}">
                    <strong>{{ $track->label() }}</strong>
                    <span>{{ $track->description() }}</span>
                </a>
            @endforeach
        </div>

        <div class="btn-row">
            <a class="btn btn--on-pine" href="{{ route('rfp.create') }}">Submit a Request for Proposal</a>
            <a class="btn" style="border-color:currentColor;color:inherit" href="{{ route('contact.engagement-desk') }}">Speak with our engagement desk</a>
        </div>

        <p class="band__micro" style="margin-top:1.5rem">
            Enquiries are acknowledged within {{ config('company.response.acknowledgement') }} and answered
            substantively within {{ config('company.response.substantive') }}. All submissions are treated as
            confidential; a mutual non-disclosure agreement is available before disclosure of scope.
        </p>
    </div>
</section>
