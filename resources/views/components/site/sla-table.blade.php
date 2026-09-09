{{--
    H-08 — SLA tier table.

    The table publishes the shape of the tiers. What binds us on a given engagement is
    what its statement of work says, agreed against the composite service levels of the
    infrastructure underneath it — which is what the note below tells the reader, and
    what clause 6 of the terms of engagement says in the contract. Do not present these
    figures as commitments anywhere without that qualification.
--}}

@props([
    'showNote' => true,
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

@if ($showNote)
    <div class="note" style="margin-top:1rem">
        @if (\App\Enums\SlaTier::ratified())
            <span class="note__tag">How these are agreed</span>
            <p>
                These targets are ratified: each has been checked against the composite service level of
                the infrastructure it runs on. Your statement of work names the tier and carries the
                figures. Where a tier includes service credits, clause 6 of the
                <a class="textlink" href="{{ route('legal.terms') }}">terms of engagement</a> sets out how
                they are claimed and capped.
            </p>
        @else
            <span class="note__tag">How these are agreed</span>
            <p>
                The tiers describe the framework we contract within. The targets that bind us on your
                engagement are the ones written into its statement of work, set against the composite
                service levels of the infrastructure underneath it — we will not sign up to an availability
                figure the platform below us cannot support. Where a tier carries service credits, clause 6
                of the <a class="textlink" href="{{ route('legal.terms') }}">terms of engagement</a> sets
                out how they are claimed and capped.
            </p>
        @endif
    </div>
@endif
