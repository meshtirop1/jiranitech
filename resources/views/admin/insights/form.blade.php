@php $editing = $insight->exists; @endphp

<x-layouts.admin
    :title="$editing ? 'Edit insight' : 'New insight'"
    subtitle="Published items appear on the homepage rail and the insights index. Leave the publish date empty to keep it as a draft."
>
    <form method="POST" action="{{ $editing ? route('admin.insights.update', $insight) : route('admin.insights.store') }}" class="adm__panel">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="adm__row">
            <div class="adm__grid">
                <div class="adm__field adm__field--wide @error('title') adm__field--invalid @enderror">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $insight->title) }}" required>
                </div>

                <div class="adm__field">
                    <label for="slug">Slug</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $insight->slug) }}">
                    <small>Leave empty to generate from the title.</small>
                </div>

                <div class="adm__field">
                    <label for="format">Format</label>
                    <select id="format" name="format" required>
                        @foreach ($formats as $format)
                            <option value="{{ $format->value }}" @selected(old('format', $insight->format?->value) === $format->value)>
                                {{ $format->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="adm__field">
                    <label for="pillar_id">Discipline</label>
                    <select id="pillar_id" name="pillar_id">
                        <option value="">None</option>
                        @foreach ($pillars as $pillar)
                            <option value="{{ $pillar->id }}" @selected((int) old('pillar_id', $insight->pillar_id) === $pillar->id)>
                                {{ $pillar->nav_title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="adm__field">
                    <label for="read_minutes">Read time (minutes)</label>
                    <input type="number" id="read_minutes" name="read_minutes" min="1" max="120" value="{{ old('read_minutes', $insight->read_minutes ?: 4) }}" required>
                </div>

                <div class="adm__field">
                    <label for="published_at">Publish date</label>
                    <input type="date" id="published_at" name="published_at" value="{{ old('published_at', $insight->published_at?->toDateString()) }}">
                    <small>Empty keeps it a draft. A future date schedules it.</small>
                </div>

                <div class="adm__field adm__field--wide @error('abstract_line') adm__field--invalid @enderror">
                    <label for="abstract_line">Abstract line</label>
                    <input type="text" id="abstract_line" name="abstract_line" maxlength="120" value="{{ old('abstract_line', $insight->abstract_line) }}" required>
                    <small>One sentence, 120 characters maximum. This is the card copy.</small>
                </div>

                <div class="adm__field adm__field--wide">
                    <label for="body">Body</label>
                    <textarea id="body" name="body" style="min-height:20rem">{{ old('body', $insight->body) }}</textarea>
                    <small>Plain text. Separate paragraphs with a blank line.</small>
                </div>

                <label class="adm__toggle adm__field--wide">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $insight->is_featured))>
                    <span><b>Feature this item</b></span>
                </label>
            </div>

            <div class="adm__actions">
                <button type="submit" class="btn btn--primary">{{ $editing ? 'Save changes' : 'Create insight' }}</button>
                <a class="btn btn--ghost" href="{{ route('admin.insights.index') }}">Cancel</a>
            </div>
        </div>
    </form>
</x-layouts.admin>
