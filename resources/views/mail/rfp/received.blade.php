@php
    $s = $submission;
    $ink = "font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:15px; line-height:23px; color:#4e4e4e;";
    $label = "font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:13px; line-height:18px; color:#6e6e6e;";
    $value = "font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:15px; line-height:21px; color:#4e4e4e;";
    $rows = array_filter([
        'Organisation' => $s->organisation,
        'Contact' => trim($s->contact_name.($s->role ? ', '.$s->role : '')),
        'Email' => $s->email,
        'Telephone' => $s->telephone,
        'Country' => $s->country,
        'Track' => $s->track?->label(),
        'Budget band' => $s->budget_band,
        'Timeline' => $s->timeline,
        'Disciplines' => is_array($s->service_interests) ? implode(', ', $s->service_interests) : null,
        'NDA requested' => $s->nda_required ? 'Yes' : 'No',
        'Came from' => $s->source_page,
    ], fn ($v) => filled($v));
@endphp

<x-mail.layout :preheader="$s->organisation.' — '.($s->track?->label() ?? 'RFP').', reply due within '.config('company.response.substantive').'.'">

    <h1 style="margin:0 0 6px; font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:24px; line-height:30px; font-weight:bold; color:#4e4e4e;">
        New request for proposal
    </h1>

    <p style="margin:0 0 22px; {{ $ink }}">
        Reference <strong style="color:#0c6e92;">{{ $s->reference }}</strong>. The sender has been
        acknowledged automatically and told to expect a substantive reply within
        {{ config('company.response.substantive') }}. Replying to this message goes straight to them.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-top:1px solid #e5e5e5;">
        @foreach ($rows as $term => $detail)
            <tr>
                <td width="150" valign="top" style="padding:9px 12px 9px 0; border-bottom:1px solid #e5e5e5; {{ $label }}">{{ $term }}</td>
                <td valign="top" style="padding:9px 0; border-bottom:1px solid #e5e5e5; {{ $value }}">{{ $detail }}</td>
            </tr>
        @endforeach
    </table>

    <h2 style="margin:28px 0 10px; font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:14px; line-height:20px; font-weight:bold; color:#4e4e4e; text-transform:uppercase;">
        Scope as written
    </h2>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f8f8f8;">
        <tr>
            <td style="padding:16px 20px; {{ $ink }}">{!! nl2br(e($s->scope_summary)) !!}</td>
        </tr>
    </table>

    <p style="margin:26px 0 0;">
        <a href="{{ route('admin.rfp.show', $s) }}" style="display:inline-block; background-color:#0c6e92; color:#ffffff; font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:14px; line-height:20px; padding:10px 18px; border-radius:3px; text-decoration:none;">
            Open it in the console
        </a>
    </p>

    <x-slot:footnote>
        Submitted from {{ $s->submitted_ip ?: 'an unrecorded address' }}. This notification goes to
        the proposals mailbox on every submission; the console keeps the full record.
    </x-slot:footnote>
</x-mail.layout>
