<x-layouts.admin
    title="Vacancies"
    subtitle="Gate G-10. A job listing is an invitation to apply. Publish a role only once it is genuinely open and funded — advertising one that is not wastes a candidate's time and costs credibility with the engineers you are trying to hire."
>
    <details class="adm__panel adm__new">
        <summary>Add a role</summary>

        <form method="POST" action="{{ route('admin.jobs.store') }}">
            @csrf
            <div class="adm__row">
                <div class="adm__grid">

                    <div class="adm__field">
                        <label for="new-title">Title</label>
                        <input type="text" id="new-title" name="title" required>
                    </div>
                    <div class="adm__field">
                        <label for="new-slug">Slug</label>
                        <input type="text" id="new-slug" name="slug" required pattern="[a-z0-9-]+">
                        <small>Lower case and hyphens. It is part of the vacancy address.</small>
                    </div>
                    <div class="adm__field">
                        <label for="new-level">Level</label>
                        <input type="text" id="new-level" name="level" required>
                    </div>
                    <div class="adm__field">
                        <label for="new-disc">Discipline <span class="adm__optional">optional</span></label>
                        <input type="text" id="new-disc" name="discipline">
                    </div>
                    <div class="adm__field">
                        <label for="new-loc">Location</label>
                        <input type="text" id="new-loc" name="location" required>
                    </div>
                    <div class="adm__field">
                        <label for="new-arr">Arrangement</label>
                        <input type="text" id="new-arr" name="arrangement" required>
                    </div>
                    <div class="adm__field adm__field--wide">
                        <label for="new-summary">Summary</label>
                        <textarea id="new-summary" name="summary" required></textarea>
                    </div>
                    <div class="adm__field adm__field--wide">
                        <label for="new-resp">Responsibilities <span class="adm__optional">optional</span></label>
                        <textarea id="new-resp" name="responsibilities" rows="4"></textarea>
                        <small>One per line.</small>
                    </div>
                    <div class="adm__field adm__field--wide">
                        <label for="new-req">Requirements <span class="adm__optional">optional</span></label>
                        <textarea id="new-req" name="requirements" rows="4"></textarea>
                        <small>One per line.</small>
                    </div>
                    <p class="adm__meta adm__field--wide">
                        Created unadvertised. Publish it separately, once the role is open and funded.
                    </p>
                </div>
                <div class="adm__actions">
                    <button type="submit" class="btn btn--primary btn--sm">Create</button>
                </div>
            </div>
        </form>
    </details>

    @foreach ($openings as $job)
        <form method="POST" action="{{ route('admin.jobs.update', $job) }}" class="adm__panel">
            @csrf
            @method('PUT')
            <div class="adm__panelhead">
                <div>
                    <h2>{{ $job->title }}</h2>
                    <p>{{ $job->level }} &middot; {{ $job->location }}</p>
                </div>
                <span class="pill {{ $job->is_published ? 'pill--closed' : 'pill--muted' }}">
                    {{ $job->is_published ? 'Advertised' : 'Draft' }}
                </span>
            </div>

            <div class="adm__row">
                <div class="adm__grid">
                    <div class="adm__field">
                        <label for="title-{{ $job->id }}">Title</label>
                        <input type="text" id="title-{{ $job->id }}" name="title" value="{{ old('title', $job->title) }}" required>
                    </div>
                    <div class="adm__field">
                        <label for="level-{{ $job->id }}">Level</label>
                        <input type="text" id="level-{{ $job->id }}" name="level" value="{{ old('level', $job->level) }}" required>
                    </div>
                    <div class="adm__field">
                        <label for="loc-{{ $job->id }}">Location</label>
                        <input type="text" id="loc-{{ $job->id }}" name="location" value="{{ old('location', $job->location) }}" required>
                    </div>
                    <div class="adm__field">
                        <label for="arr-{{ $job->id }}">Working arrangement</label>
                        <input type="text" id="arr-{{ $job->id }}" name="arrangement" value="{{ old('arrangement', $job->arrangement) }}" required>
                    </div>
                    <div class="adm__field adm__field--wide">
                        <label for="sum-{{ $job->id }}">Summary</label>
                        <textarea id="sum-{{ $job->id }}" name="summary" required>{{ old('summary', $job->summary) }}</textarea>
                    </div>
                    <label class="adm__toggle adm__field--wide">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $job->is_published))>
                        <span>
                            <b>Advertise this role</b>
                            <small>Confirm it is open and funded first. Publishing stamps the posted date automatically.</small>
                        </span>
                    </label>
                </div>
                <div class="adm__actions">
                    <button type="submit" class="btn btn--primary btn--sm">Save</button>
                </div>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" class="adm__danger"
              onsubmit="return confirm('Delete this record? Anything published from it disappears from the site.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
        </form>
    @endforeach
</x-layouts.admin>
