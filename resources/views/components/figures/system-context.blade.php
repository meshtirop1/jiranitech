{{--
    H-02 hero visual — abstracted system context.

    A layered reference topology of the kind we actually deliver, drawn in the brand's
    two tones. Explicitly not stock photography, a globe, or a network-node mesh: the
    hero of an engineering firm should show the shape of the work.
--}}

<figure style="margin:0">
    <svg class="sysfigure" viewBox="0 0 480 376" role="img" width="100%"
         aria-label="Layered reference architecture: channel, edge, core services, integration and data platform, with governance applied across every layer.">

        <text x="12" y="22" font-family="var(--font-sans)" font-size="10" letter-spacing="1.6"
              fill="var(--brass)">REFERENCE TOPOLOGY</text>

        @php
            $rows = [
                ['label' => 'CHANNEL',     'y' => 44,  'cells' => ['Web', 'Mobile', 'Partner API'],                     'tint' => false],
                ['label' => 'EDGE',        'y' => 112, 'cells' => ['API gateway · WAF · rate limiting'],                'tint' => true],
                ['label' => 'CORE',        'y' => 176, 'cells' => ['Identity', 'Domain services', 'Orchestration'],     'tint' => false],
                ['label' => 'INTEGRATION', 'y' => 242, 'cells' => ['Payment rails', 'Systems of record'],               'tint' => false],
                ['label' => 'DATA',        'y' => 306, 'cells' => ['Data platform · analytics · retention'],            'tint' => true],
            ];
        @endphp

        {{-- connectors sit behind the boxes --}}
        @foreach ([[84, 112], [152, 176], [216, 242], [282, 306]] as [$from, $to])
            <line x1="286" y1="{{ $from }}" x2="286" y2="{{ $to }}" stroke="var(--rule-strong)" stroke-width="1"/>
        @endforeach

        @foreach ($rows as $row)
            @php
                $count = count($row['cells']);
                $width = (364 - (8 * ($count - 1))) / $count;
            @endphp

            <text x="88" y="{{ $row['y'] + 24 }}" text-anchor="end" font-family="var(--font-sans)"
                  font-size="9.5" letter-spacing="1.3" fill="var(--muted)">{{ $row['label'] }}</text>

            @foreach ($row['cells'] as $index => $cell)
                @php $x = 104 + $index * ($width + 8); @endphp
                <rect x="{{ $x }}" y="{{ $row['y'] }}" width="{{ $width }}" height="40"
                      fill="{{ $row['tint'] ? 'var(--pine-soft)' : 'var(--surface)' }}"
                      stroke="{{ $row['tint'] ? 'var(--pine)' : 'var(--rule-strong)' }}" stroke-width="1"/>
                <text x="{{ $x + $width / 2 }}" y="{{ $row['y'] + 24 }}" text-anchor="middle"
                      font-family="var(--font-sans)" font-size="11.5"
                      fill="{{ $row['tint'] ? 'var(--pine-ink)' : 'var(--ink-2)' }}">{{ $cell }}</text>
            @endforeach
        @endforeach

        <line x1="104" y1="360" x2="468" y2="360" stroke="var(--brass)" stroke-width="1" stroke-dasharray="3 3"/>
        <text x="286" y="356" text-anchor="middle" font-family="var(--font-sans)" font-size="9.5"
              letter-spacing="1.3" fill="var(--brass)">OBSERVABILITY · POLICY · AUDIT — EVERY LAYER</text>
    </svg>
</figure>
