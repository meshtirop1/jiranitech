<x-layouts.admin :title="$submission->organisation" :subtitle="$submission->reference">
    <x-slot:actions>
        <form method="POST" action="{{ route('admin.rfp.acknowledge', $submission) }}">
            @csrf
            @method('PUT')
            <button type="submit" class="btn {{ $submission->acknowledged_at ? 'btn--ghost' : 'btn--primary' }} btn--sm">
                {{ $submission->acknowledged_at ? 'Mark unacknowledged' : 'Mark acknowledged' }}
            </button>
        </form>
    </x-slot:actions>

    <div class="adm__panel">
        <div class="adm__panelhead">
            <div><h2>Enquiry</h2></div>
            <span class="pill pill--muted">{{ $submission->track->label() }}</span>
        </div>
        <div class="adm__row">
            <div class="adm__grid">
                <div class="adm__field">
                    <label>Contact</label>
                    <p style="margin:0;font-size:.9rem">
                        {{ $submission->contact_name }}@if ($submission->role), {{ $submission->role }}@endif<br>
                        <a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a>
                        @if ($submission->telephone)<br>{{ $submission->telephone }}@endif
                        @if ($submission->country)<br>{{ $submission->country }}@endif
                    </p>
                </div>
                <div class="adm__field">
                    <label>Commercial</label>
                    <p style="margin:0;font-size:.9rem">
                        Budget: {{ $submission->budget_band ?: 'not stated' }}<br>
                        Timeline: {{ $submission->timeline ?: 'not stated' }}<br>
                        NDA requested: {{ $submission->nda_required ? 'yes' : 'no' }}
                    </p>
                </div>
                <div class="adm__field">
                    <label>Received</label>
                    <p style="margin:0;font-size:.9rem">
                        {{ $submission->created_at->format('j F Y, H:i') }}<br>
                        {{ $submission->created_at->diffForHumans() }}<br>
                        IP {{ $submission->submitted_ip ?: 'not recorded' }}
                    </p>
                </div>

                <div class="adm__field adm__field--wide">
                    <label>Scope as submitted</label>
                    <p style="margin:0;font-size:.93rem;line-height:1.6;white-space:pre-wrap">{{ $submission->scope_summary }}</p>
                </div>

                @if ($submission->service_interests)
                    <div class="adm__field adm__field--wide">
                        <label>Disciplines flagged</label>
                        <ul class="taglist">
                            @foreach ($submission->service_interests as $slug)
                                <li>{{ $slug }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="adm__actions">
                <a class="btn btn--primary btn--sm"
                   href="mailto:{{ $submission->email }}?subject={{ rawurlencode('Re: '.$submission->reference.' — '.$submission->organisation) }}">
                    Reply by email
                </a>
                <a class="btn btn--ghost btn--sm" href="{{ route('admin.rfp.index') }}">Back to inbox</a>
            </div>
        </div>
    </div>
</x-layouts.admin>
