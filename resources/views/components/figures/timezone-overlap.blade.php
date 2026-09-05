{{--
    H-07, advantage 02 — working-day overlap, drawn to a single scale.

    One axis in East Africa Time, 06:00 to 24:00. Each bar is a 09:00-17:00 local
    working day for that market converted into EAT; the shaded column is our own
    08:30-17:30 operating window, and the solid segment of each bar is the intersection.
    Overlap hours in the labels are read off this scale, not asserted separately.
--}}

<figure style="margin:0">
    <figcaption class="eyebrow" style="margin-bottom:.9rem">
        Working-day overlap with {{ config('company.city') }} — hours in East Africa Time
    </figcaption>

    <div class="tablewrap" style="padding:1.25rem 1rem">
        <svg viewBox="0 0 860 292" role="img" width="100%" style="min-width:640px;display:block"
             aria-label="Bar chart of working-day overlap. India 6.0 hours, Gulf 7.5 hours, continental Europe 7.5 hours, United Kingdom 6.5 hours, United States East Coast 1.5 hours on standard office hours.">

            {{-- our operating window --}}
            <rect x="283.33" y="52" width="300" height="176" fill="var(--pine-soft)"/>
            <line x1="283.33" y1="52" x2="283.33" y2="228" stroke="var(--pine)" stroke-width="1"/>
            <line x1="583.33" y1="52" x2="583.33" y2="228" stroke="var(--pine)" stroke-width="1"/>
            <text x="433" y="44" text-anchor="middle" font-family="var(--font-sans)" font-size="11"
                  letter-spacing="1.2" fill="var(--pine)">OUR HOURS 08:30–17:30 EAT</text>

            @php
                $rows = [
                    ['label' => 'India (UTC+5:30)',        'y' => 64,  'x' => 216.67, 'ox' => 283.33, 'ow' => 200,    'hours' => '6.0 h'],
                    ['label' => 'Gulf (UTC+4)',            'y' => 98,  'x' => 266.67, 'ox' => 283.33, 'ow' => 250,    'hours' => '7.5 h'],
                    ['label' => 'Continental Europe',      'y' => 132, 'x' => 333.33, 'ox' => 333.33, 'ow' => 250,    'hours' => '7.5 h'],
                    ['label' => 'United Kingdom',          'y' => 166, 'x' => 366.67, 'ox' => 366.67, 'ow' => 216.67, 'hours' => '6.5 h'],
                    ['label' => 'United States, Eastern',  'y' => 200, 'x' => 533.33, 'ox' => 533.33, 'ow' => 50,     'hours' => '1.5 h'],
                ];
            @endphp

            @foreach ($rows as $row)
                <text x="190" y="{{ $row['y'] + 12 }}" text-anchor="end" font-family="var(--font-sans)"
                      font-size="12.5" fill="var(--ink-2)">{{ $row['label'] }}</text>
                <rect x="{{ $row['x'] }}" y="{{ $row['y'] }}" width="266.67" height="16"
                      fill="var(--surface-2)" stroke="var(--rule-strong)" stroke-width="1"/>
                <rect x="{{ $row['ox'] }}" y="{{ $row['y'] }}" width="{{ $row['ow'] }}" height="16"
                      fill="var(--pine)"/>
                <text x="{{ $row['x'] + 272 }}" y="{{ $row['y'] + 12 }}" font-family="var(--font-sans)"
                      font-size="11" fill="var(--brass)">{{ $row['hours'] }}</text>
            @endforeach

            {{-- axis --}}
            <line x1="200" y1="236" x2="800" y2="236" stroke="var(--rule-strong)" stroke-width="1"/>
            @foreach ([6, 9, 12, 15, 18, 21, 24] as $hour)
                @php $x = 200 + ($hour - 6) * 33.333; @endphp
                <line x1="{{ $x }}" y1="236" x2="{{ $x }}" y2="242" stroke="var(--rule-strong)" stroke-width="1"/>
                <text x="{{ $x }}" y="258" text-anchor="middle" font-family="var(--font-sans)"
                      font-size="11" fill="var(--muted)">{{ str_pad((string) ($hour % 24), 2, '0', STR_PAD_LEFT) }}:00</text>
            @endforeach

            <text x="200" y="282" font-family="var(--font-sans)" font-size="11.5" fill="var(--muted)">
                Each bar is a 09:00–17:00 local working day expressed in EAT. Solid segment = overlap with our hours.
            </text>
        </svg>
    </div>

    <p class="small muted" style="margin-top:.9rem;max-width:66ch">
        The Gulf, continental Europe, the United Kingdom and South Asia all fall within a six-hour or
        better daily overlap on standard hours. The United States East Coast does not: on 08:30–17:30 EAT
        the overlap is 1.5 hours, extending to approximately 3.5 hours where an engagement is staffed on a
        staggered afternoon shift. We state that arrangement in the engagement terms rather than implying
        it here.
    </p>
</figure>
