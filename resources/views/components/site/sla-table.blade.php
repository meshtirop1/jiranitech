{{--
    H-08 — SLA tier table.

    Publication gate G-05: these targets are a structural illustration of the table, not
    approved commercial terms. The gate note below renders until the values have been
    ratified against the composite SLA of the underlying cloud providers, and is removed
    by deleting the note — not by editing the figures silently.
--}}

@props([
    'showGate' => true,
])

<div class="tablewrap">
    <table class="data">
        <caption class="sr-only">Service level tiers, availability targets and response commitments</caption>
        <thead>
            <tr>
                <th scope="col">Tier</th>
                <th scope="col">Availability target</th>
                <th scope="col">P1 response</th>
                <th scope="col">P1 resolution target</th>
                <th scope="col">Coverage</th>
                <th scope="col">Service credits</th>
            </tr>
        </thead>
        <tbody>
            @foreach (\App\Enums\SlaTier::cases() as $tier)
                <tr>
                    <th scope="row" style="font-weight:700">{{ $tier->label() }}</th>
                    <td class="num">{{ $tier->availabilityTarget() }}</td>
                    <td class="num">{{ $tier->priorityOneResponse() }}</td>
                    <td class="num">{{ $tier->priorityOneResolution() }}</td>
                    <td class="num">{{ $tier->coverage() }}</td>
                    <td class="num">{{ $tier->hasServiceCredits() ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if ($showGate)
    <div class="note note--gate" style="margin-top:1rem">
        <span class="note__tag">Publication gate G-05 — outstanding</span>
        <p>
            These values illustrate the structure of the table. Each figure must be confirmed by whoever
            will be contractually bound by it, and the availability targets must be achievable on the
            underlying cloud provider SLAs before publication. Publishing a 99.95% target on infrastructure
            whose own composite SLA is lower creates an obligation that cannot be met.
        </p>
    </div>
@endif
