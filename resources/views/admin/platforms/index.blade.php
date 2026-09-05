<x-layouts.admin
    title="Platforms"
    subtitle="Gate G-03. Scale figures are commercially sensitive, so they stay hidden until the group clears them in writing. The qualitative operator argument renders either way."
>
    @foreach ($platforms as $platform)
        <form method="POST" action="{{ route('admin.platforms.update', $platform) }}" class="adm__panel">
            @csrf
            @method('PUT')
            <div class="adm__panelhead">
                <div>
                    <h2>{{ $platform->title }}</h2>
                    <p><code>/platforms/{{ $platform->slug }}</code></p>
                </div>
                <span class="pill {{ $platform->cleared_for_disclosure ? 'pill--closed' : 'pill--open' }}">
                    {{ $platform->cleared_for_disclosure ? 'Figures public' : 'Figures withheld' }}
                </span>
            </div>

            <div class="adm__row">
                <div class="adm__grid">
                    <div class="adm__field">
                        <label for="title-{{ $platform->id }}">Title</label>
                        <input type="text" id="title-{{ $platform->id }}" name="title" value="{{ old('title', $platform->title) }}" required>
                    </div>
                    <div class="adm__field">
                        <label for="since-{{ $platform->id }}">Operating since</label>
                        <input type="text" id="since-{{ $platform->id }}" name="operating_since" value="{{ old('operating_since', $platform->operating_since) }}">
                    </div>
                    <div class="adm__field">
                        <label for="url-{{ $platform->id }}">Public URL</label>
                        <input type="url" id="url-{{ $platform->id }}" name="external_url" value="{{ old('external_url', $platform->external_url) }}">
                        <small>Linked only from this platform page — never from the site chrome, where it would read as the holding company.</small>
                    </div>
                    <div class="adm__field">
                        <label for="ulabel-{{ $platform->id }}">Link label</label>
                        <input type="text" id="ulabel-{{ $platform->id }}" name="external_label" value="{{ old('external_label', $platform->external_label) }}">
                    </div>
                    <div class="adm__field adm__field--wide">
                        <label for="ctx-{{ $platform->id }}">System context</label>
                        <textarea id="ctx-{{ $platform->id }}" name="system_context" required>{{ old('system_context', $platform->system_context) }}</textarea>
                    </div>

                    <div class="adm__field adm__field--wide">
                        <label>Scale figures</label>
                        <small>Only shown publicly once cleared below.</small>
                        @foreach ($platform->scale_metrics ?? [] as $i => $row)
                            <div class="adm__grid" style="margin-top:.4rem">
                                <input type="text" name="scale_metrics[{{ $i }}][label]" value="{{ $row['label'] ?? '' }}" placeholder="Label">
                                <input type="text" name="scale_metrics[{{ $i }}][value]" value="{{ $row['value'] ?? '' }}" placeholder="Value">
                            </div>
                        @endforeach
                    </div>

                    <label class="adm__toggle adm__field--wide">
                        <input type="checkbox" name="cleared_for_disclosure" value="1" @checked(old('cleared_for_disclosure', $platform->cleared_for_disclosure))>
                        <span>
                            <b>Cleared for external disclosure</b>
                            <small>Tick only with written clearance from {{ config('company.parent.name') }}. These are commercially sensitive figures.</small>
                        </span>
                    </label>
                </div>
                <div class="adm__actions">
                    <button type="submit" class="btn btn--primary btn--sm">Save</button>
                </div>
            </div>
        </form>
    @endforeach
</x-layouts.admin>
