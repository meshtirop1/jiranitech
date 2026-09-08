<x-layouts.admin
    title="Metrics"
    subtitle="Gate G-01. Every figure the site publishes carries the basis it rests on. A metric with no basis cannot be saved, and an unpublished one never reaches a page."
>
    <details class="adm__panel adm__new">
        <summary>Add a metric</summary>

        <form method="POST" action="{{ route('admin.metrics.store') }}">
            @csrf
            <div class="adm__row">
                <div class="adm__grid">

                    <div class="adm__field">
                        <label for="new-key">Key</label>
                        <input type="text" id="new-key" name="key" required pattern="[a-z0-9_]+">
                        <small>Lower case and underscores. Templates reference the figure by this.</small>
                    </div>
                    <div class="adm__field">
                        <label for="new-label">Label</label>
                        <input type="text" id="new-label" name="label" required>
                    </div>
                    <div class="adm__field">
                        <label for="new-value">Value</label>
                        <input type="text" id="new-value" name="value" required>
                        <small>Exactly as it should read, including any unit or symbol.</small>
                    </div>
                    <div class="adm__field">
                        <label for="new-eff">Effective on</label>
                        <input type="date" id="new-eff" name="effective_on" required>
                    </div>
                    <div class="adm__field adm__field--wide">
                        <label for="new-basis">Basis</label>
                        <textarea id="new-basis" name="basis" required></textarea>
                        <small>What the figure rests on: the source, the period and the method. A figure without one does not go on the site.</small>
                    </div>
                    <div class="adm__field adm__field--wide">
                        <label for="new-sub">Substantiation reference <span class="adm__optional">optional</span></label>
                        <input type="text" id="new-sub" name="substantiation_ref">
                    </div>
                    <p class="adm__meta adm__field--wide">
                        Created unpublished. Publish it once somebody has read the basis it carries.
                    </p>
                </div>
                <div class="adm__actions">
                    <button type="submit" class="btn btn--primary btn--sm">Create</button>
                </div>
            </div>
        </form>
    </details>

    @foreach ($metrics as $metric)
        <form method="POST" action="{{ route('admin.metrics.update', $metric) }}" class="adm__panel">
            @csrf
            @method('PUT')
            <div class="adm__panelhead">
                <div>
                    <h2>{{ $metric->label }}</h2>
                    <p><code>{{ $metric->key }}</code></p>
                </div>
                <span class="pill {{ $metric->is_published ? 'pill--closed' : 'pill--open' }}">
                    {{ $metric->is_published ? 'Published' : 'Withheld' }}
                </span>
            </div>

            <div class="adm__row">
                <div class="adm__grid">
                    <div class="adm__field">
                        <label for="label-{{ $metric->id }}">Label</label>
                        <input type="text" id="label-{{ $metric->id }}" name="label" value="{{ old('label', $metric->label) }}" required>
                    </div>
                    <div class="adm__field">
                        <label for="value-{{ $metric->id }}">Value</label>
                        <input type="text" id="value-{{ $metric->id }}" name="value" value="{{ old('value', $metric->value) }}" required>
                    </div>
                    <div class="adm__field">
                        <label for="ref-{{ $metric->id }}">Substantiation reference</label>
                        <input type="text" id="ref-{{ $metric->id }}" name="substantiation_ref" value="{{ old('substantiation_ref', $metric->substantiation_ref) }}">
                        <small>Board paper, certificate or contract — whatever a reviewer could be pointed at.</small>
                    </div>
                    <div class="adm__field adm__field--wide">
                        <label for="basis-{{ $metric->id }}">Basis <span style="color:var(--clay)">*</span></label>
                        <textarea id="basis-{{ $metric->id }}" name="basis" required>{{ old('basis', $metric->basis) }}</textarea>
                        <small>What stands behind this number. Required, because it is the whole mechanism of gate G-01.</small>
                    </div>
                    <label class="adm__toggle adm__field--wide">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $metric->is_published))>
                        <span>
                            <b>Publish this figure</b>
                            <small>Published metrics fill the homepage hero bar, first four by sort order.</small>
                        </span>
                    </label>
                </div>
                <div class="adm__actions">
                    <button type="submit" class="btn btn--primary btn--sm">Save</button>
                </div>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.metrics.destroy', $metric) }}" class="adm__danger"
              onsubmit="return confirm('Delete this record? Anything published from it disappears from the site.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
        </form>
    @endforeach
</x-layouts.admin>
