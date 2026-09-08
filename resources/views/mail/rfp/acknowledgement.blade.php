@php
    $ink = "font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:15px; line-height:23px; color:#4e4e4e;";
    $label = "font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:13px; line-height:18px; color:#6e6e6e;";
    $value = "font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:15px; line-height:21px; color:#4e4e4e;";
@endphp

<x-mail.layout :preheader="'Your reference is '.$submission->reference.'. A qualified engineering lead replies within '.config('company.response.substantive').'.'">

    <h1 style="margin:0 0 6px; font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:26px; line-height:32px; font-weight:bold; color:#4e4e4e;">
        We have your request
    </h1>

    <p style="margin:0 0 24px; {{ $ink }}">
        {{ $submission->contact_name }}, thank you. Your request for proposal reached us and
        has been logged against the reference below. Quote it in any correspondence about
        this enquiry.
    </p>

    {{-- The reference, given its own block because it is the one thing the
         recipient may need to find again in six weeks. --}}
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f8f8f8; border-left:3px solid #1cbbed;">
        <tr>
            <td style="padding:16px 20px;">
                <p style="margin:0 0 2px; {{ $label }}">Your reference</p>
                <p style="margin:0; font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:22px; line-height:28px; font-weight:bold; color:#0c6e92; letter-spacing:0.5px;">
                    {{ $submission->reference }}
                </p>
            </td>
        </tr>
    </table>

    <h2 style="margin:32px 0 12px; font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:14px; line-height:20px; font-weight:bold; color:#4e4e4e; text-transform:uppercase;">
        What happens next
    </h2>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        @foreach ([
            'A qualified engineering lead reads it. Not a sales development representative, and not an automated scoring tool.',
            'If the brief is one we can serve well, that lead replies within '.config('company.response.substantive').' with either a scoping call or a straight answer that we are not the right firm.',
            'You will not be added to a mailing list, and nobody will telephone you before that reply.',
        ] as $i => $step)
            <tr>
                <td width="28" valign="top" style="padding:0 0 12px;">
                    <span style="display:inline-block; font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:14px; line-height:20px; font-weight:bold; color:#0c6e92;">{{ $i + 1 }}.</span>
                </td>
                <td valign="top" style="padding:0 0 12px; {{ $ink }}">{{ $step }}</td>
            </tr>
        @endforeach
    </table>

    <h2 style="margin:28px 0 12px; font-family:'PT Sans',Arial,Helvetica,sans-serif; font-size:14px; line-height:20px; font-weight:bold; color:#4e4e4e; text-transform:uppercase;">
        What you sent us
    </h2>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-top:1px solid #e5e5e5;">
        @foreach ($summary as $term => $detail)
            <tr>
                <td width="170" valign="top" style="padding:10px 12px 10px 0; border-bottom:1px solid #e5e5e5; {{ $label }}">{{ $term }}</td>
                <td valign="top" style="padding:10px 0; border-bottom:1px solid #e5e5e5; {{ $value }}">{{ $detail }}</td>
            </tr>
        @endforeach
    </table>

    <p style="margin:24px 0 0; {{ $ink }}">
        If any of that is wrong, reply to this message and the correction reaches the same
        engineering lead.
    </p>

    <x-slot:footnote>
        This message was sent to {{ $submission->email }} because a request for proposal was
        submitted from {{ parse_url(config('app.url'), PHP_URL_HOST) }} carrying that address.
        It is a one-off acknowledgement, not a subscription — there is nothing to unsubscribe
        from. If it was not you, reply and tell us, and we will delete the submission.
    </x-slot:footnote>
</x-mail.layout>
