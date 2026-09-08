@props(['field', 'record' => null, 'idPrefix' => 'new'])

@php
    $id = $field->name.'-'.$idPrefix;
    $stored = $record ? $field->toForm($record->{$field->name}) : '';
    $value = old($field->name, $stored);
    $isRequired = in_array('required', $field->rules, true);
@endphp

@if ($field->type === 'checkbox')
    <label class="adm__toggle adm__field--wide" for="{{ $id }}">
        <input type="checkbox" id="{{ $id }}" name="{{ $field->name }}" value="1"
               @checked(old($field->name, $record ? (bool) $record->{$field->name} : false))>
        <span><b>{{ $field->label }}</b>@if ($field->help) <em>{{ $field->help }}</em>@endif</span>
    </label>
@else
    <div @class(['adm__field', 'adm__field--wide' => $field->wide])>
        <label for="{{ $id }}">
            {{ $field->label }}
            @unless ($isRequired)<span class="adm__optional">optional</span>@endunless
        </label>

        @if ($field->type === 'textarea' || $field->type === 'lines')
            <textarea id="{{ $id }}" name="{{ $field->name }}"
                      rows="{{ $field->type === 'lines' ? 4 : 3 }}"
                      @required($isRequired)>{{ $value }}</textarea>
        @elseif ($field->type === 'select')
            <select id="{{ $id }}" name="{{ $field->name }}" @required($isRequired)>
                @unless ($isRequired)<option value="">—</option>@endunless
                @foreach ($field->options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                @endforeach
            </select>
        @else
            <input type="{{ $field->type === 'number' ? 'number' : ($field->type === 'date' ? 'date' : 'text') }}"
                   id="{{ $id }}" name="{{ $field->name }}" value="{{ $value }}"
                   @required($isRequired)>
        @endif

        @if ($field->help)
            <small>{{ $field->help }}</small>
        @endif
    </div>
@endif
