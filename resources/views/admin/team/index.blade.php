<x-layouts.admin
    title="Leadership"
    subtitle="Gate G-09. Each card states a post and what it is accountable for. Until a real name is supplied the card reads “Appointment to be announced”. Procurement reviewers verify these names, so never invent one."
>
    <details class="adm__panel adm__new">
        <summary>Add a post</summary>

        <form method="POST" action="{{ route('admin.team.store') }}">
            @csrf
            <div class="adm__row">
                <div class="adm__grid">

                    <div class="adm__field">
                        <label for="new-role">Post</label>
                        <input type="text" id="new-role" name="role_title" required>
                        <small>The accountability, not the person. "Head of Information Security", not a name.</small>
                    </div>
                    <div class="adm__field">
                        <label for="new-name">Appointee name <span class="adm__optional">optional</span></label>
                        <input type="text" id="new-name" name="name">
                        <small>Leave empty while the post is unfilled. A real person only, with their consent.</small>
                    </div>
                    <div class="adm__field">
                        <label for="new-discipline">Discipline <span class="adm__optional">optional</span></label>
                        <input type="text" id="new-discipline" name="discipline">
                    </div>
                    <div class="adm__field">
                        <label for="new-sort">Sort order <span class="adm__optional">optional</span></label>
                        <input type="number" id="new-sort" name="sort_order" min="0">
                    </div>
                    <div class="adm__field adm__field--wide">
                        <label for="new-acc">Accountability</label>
                        <textarea id="new-acc" name="accountability" required></textarea>
                        <small>What this post answers for. Written so a client escalating knows when to reach it.</small>
                    </div>
                    <label class="adm__toggle adm__field--wide">
                        <input type="checkbox" name="is_published" value="1">
                        <span><b>Show this post on the leadership page</b></span>
                    </label>
                </div>
                <div class="adm__actions">
                    <button type="submit" class="btn btn--primary btn--sm">Create</button>
                </div>
            </div>
        </form>
    </details>

    @foreach ($members as $member)
        <form method="POST" action="{{ route('admin.team.update', $member) }}" class="adm__panel">
            @csrf
            @method('PUT')
            <div class="adm__panelhead">
                <div><h2>{{ $member->role_title }}</h2></div>
                <span class="pill {{ $member->isAnnounced() ? 'pill--closed' : 'pill--open' }}">
                    {{ $member->isAnnounced() ? 'Named' : 'Vacant' }}
                </span>
            </div>

            <div class="adm__row">
                <div class="adm__grid">
                    <div class="adm__field">
                        <label for="name-{{ $member->id }}">Appointee name</label>
                        <input type="text" id="name-{{ $member->id }}" name="name" value="{{ old('name', $member->name) }}">
                        <small>Leave empty while the post is unfilled. Add a real person only, with their consent to publication.</small>
                    </div>
                    <div class="adm__field">
                        <label for="role-{{ $member->id }}">Post</label>
                        <input type="text" id="role-{{ $member->id }}" name="role_title" value="{{ old('role_title', $member->role_title) }}" required>
                    </div>
                    <div class="adm__field">
                        <label for="photo-{{ $member->id }}">Photograph path</label>
                        <input type="text" id="photo-{{ $member->id }}" name="photo_path" value="{{ old('photo_path', $member->photo_path) }}">
                        <small>Optional. A path under public/, for example /images/team/name.jpg</small>
                    </div>
                    <div class="adm__field adm__field--wide">
                        <label for="acc-{{ $member->id }}">Accountability</label>
                        <textarea id="acc-{{ $member->id }}" name="accountability" required>{{ old('accountability', $member->accountability) }}</textarea>
                    </div>
                    <label class="adm__toggle adm__field--wide">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $member->is_published))>
                        <span><b>Show this post on the leadership page</b></span>
                    </label>
                </div>
                <div class="adm__actions">
                    <button type="submit" class="btn btn--primary btn--sm">Save</button>
                </div>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.team.destroy', $member) }}" class="adm__danger"
              onsubmit="return confirm('Delete this record? Anything published from it disappears from the site.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
        </form>
    @endforeach
</x-layouts.admin>
