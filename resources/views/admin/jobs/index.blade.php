<x-layouts.admin
    title="Vacancies"
    subtitle="Gate G-10. A job listing is an invitation to apply. Publish a role only once it is genuinely open and funded — advertising one that is not wastes a candidate's time and costs credibility with the engineers you are trying to hire."
>
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
    @endforeach
</x-layouts.admin>
