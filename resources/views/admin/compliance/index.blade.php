<x-layouts.admin
    title="Compliance register"
    subtitle="Gate G-02. A standard you follow but have not certified renders as “Aligned — not certified”. Misstating this is the fastest route to disqualification in a vendor review, so a certified claim requires an evidence URL."
>
    <details class="adm__panel adm__new">
        <summary>Add a standard</summary>

        <form method="POST" action="{{ route('admin.compliance.store') }}">
            @csrf
            <div class="adm__row">
                <div class="adm__grid">

                    <div class="adm__field">
                        <label for="new-standard">Standard</label>
                        <input type="text" id="new-standard" name="standard" required>
                        <small>As it is formally named, for example "ISO/IEC 27001:2022".</small>
                    </div>
                    <div class="adm__field">
                        <label for="new-status">Status</label>
                        <select id="new-status" name="status" required>
                            @foreach (\App\Enums\ComplianceStatus::cases() as $case)
                                <option value="{{ $case->value }}">{{ $case->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm__field">
                        <label for="new-reviewed">Reviewed on</label>
                        <input type="date" id="new-reviewed" name="reviewed_on" required>
                    </div>
                    <div class="adm__field">
                        <label for="new-evidence">Evidence URL <span class="adm__optional">optional</span></label>
                        <input type="url" id="new-evidence" name="evidence_url">
                        <small>Required before a standard can be marked certified. Reviewers follow it.</small>
                    </div>
                </div>
                <div class="adm__actions">
                    <button type="submit" class="btn btn--primary btn--sm">Create</button>
                </div>
            </div>
        </form>
    </details>

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

        <form method="POST" action="{{ route('admin.compliance.destroy', $claim) }}" class="adm__danger"
              onsubmit="return confirm('Delete this record? Anything published from it disappears from the site.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
        </form>
    @endforeach
</x-layouts.admin>
