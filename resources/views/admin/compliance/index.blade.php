<x-layouts.admin
    title="Compliance register"
    subtitle="Gate G-02. A standard you follow but have not certified renders as “Aligned — not certified”. Misstating this is the fastest route to disqualification in a vendor review, so a certified claim requires an evidence URL."
>
    @foreach ($claims as $claim)
        <form method="POST" action="{{ route('admin.compliance.update', $claim) }}" class="adm__panel">
            @csrf
            @method('PUT')
            <div class="adm__panelhead">
                <div><h2>{{ $claim->standard }}</h2></div>
                <span class="pill {{ $claim->status->isAssertable() ? 'pill--closed' : 'pill--muted' }}">
                    {{ $claim->status->label() }}
                </span>
            </div>

            <div class="adm__row">
                <div class="adm__grid">
                    <div class="adm__field">
                        <label for="status-{{ $claim->id }}">Status</label>
                        <select id="status-{{ $claim->id }}" name="status" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected(old('status', $claim->status->value) === $status->value)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm__field">
                        <label for="reviewed-{{ $claim->id }}">Last reviewed</label>
                        <input type="date" id="reviewed-{{ $claim->id }}" name="reviewed_on" value="{{ old('reviewed_on', $claim->reviewed_on?->toDateString()) }}" required>
                    </div>
                    <div class="adm__field adm__field--wide @error('evidence_url') adm__field--invalid @enderror">
                        <label for="evidence-{{ $claim->id }}">Evidence URL</label>
                        <input type="url" id="evidence-{{ $claim->id }}" name="evidence_url" value="{{ old('evidence_url', $claim->evidence_url) }}">
                        <small>Required if you mark this certified. Reviewers check the certificate against the accreditation registry.</small>
                    </div>
                </div>
                <div class="adm__actions">
                    <button type="submit" class="btn btn--primary btn--sm">Save</button>
                </div>
            </div>
        </form>
    @endforeach
</x-layouts.admin>
