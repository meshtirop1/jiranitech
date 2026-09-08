@php
    /** Renders one control from its declaration, for both the new and the edit form. */
    $control = function ($field, $record, $idPrefix) {
        $value = old($field->name, $record ? $field->toForm($record->{$field->name}) : '');

        return compact('field', 'value', 'idPrefix');
    };
@endphp

<x-layouts.admin :title="$type->label" :subtitle="$type->subtitle">

    <nav class="adm__tabs" aria-label="Content types">
        @foreach ($types as $other)
            <a href="{{ route('admin.content.index', $other->key) }}"
               @class(['adm__tab', 'is-current' => $other->key === $type->key])
               @if ($other->key === $type->key) aria-current="page" @endif>
                {{ $other->label }}
            </a>
        @endforeach
    </nav>

    {{-- ------------------------------------------------------------ new --- --}}

    <details class="adm__panel adm__new">
        <summary>Add a new {{ Illuminate\Support\Str::lower(Illuminate\Support\Str::singular($type->label)) }}</summary>

        <form method="POST" action="{{ route('admin.content.store', $type->key) }}">
            @csrf
            <div class="adm__row">
                <div class="adm__grid">
                    @foreach ($type->fields as $field)
                        <x-admin.content-field :field="$field" :record="null" id-prefix="new" />
                    @endforeach
                </div>
                <div class="adm__actions">
                    <button type="submit" class="btn btn--primary btn--sm">Create</button>
                </div>
            </div>
        </form>
    </details>

    {{-- -------------------------------------------------------- existing --- --}}

    @forelse ($records as $record)
        <div class="adm__panel">
            <div class="adm__panelhead">
                <div>
                    <h2>{{ $record->{$type->titleColumn} }}</h2>
                    @if ($record->slug ?? null)
                        <p class="adm__meta">{{ $record->slug }}</p>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('admin.content.update', [$type->key, $record->id]) }}">
                @csrf
                @method('PUT')
                <div class="adm__row">
                    <div class="adm__grid">
                        @foreach ($type->fields as $field)
                            <x-admin.content-field :field="$field" :record="$record" :id-prefix="$record->id" />
                        @endforeach
                    </div>
                    <div class="adm__actions">
                        <button type="submit" class="btn btn--primary btn--sm">Save</button>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.content.destroy', [$type->key, $record->id]) }}"
                  class="adm__danger"
                  onsubmit="return confirm('Delete “{{ $record->{$type->titleColumn} }}”? Any page it publishes will stop resolving.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
            </form>
        </div>
    @empty
        <div class="adm__panel">
            <p class="adm__empty">
                Nothing here yet. Use the form above to add the first
                {{ Illuminate\Support\Str::lower(Illuminate\Support\Str::singular($type->label)) }}.
            </p>
        </div>
    @endforelse

</x-layouts.admin>
