{{ $submission->contact_name }}, thank you.

Your request for proposal reached us and has been logged against the reference
below. Quote it in any correspondence about this enquiry.

YOUR REFERENCE: {{ $submission->reference }}


WHAT HAPPENS NEXT

1. A qualified engineering lead reads it. Not a sales development
   representative, and not an automated scoring tool.

2. If the brief is one we can serve well, that lead replies within
   {{ config('company.response.substantive') }} with either a scoping call or a straight answer
   that we are not the right firm.

3. You will not be added to a mailing list, and nobody will telephone you
   before that reply.


WHAT YOU SENT US
@foreach ($summary as $term => $detail)

{{ $term }}: {{ $detail }}
@endforeach


If any of that is wrong, reply to this message and the correction reaches the
same engineering lead.

--
{{ config('company.legal_name') }}
The enterprise technology division of {{ config('company.parent.name') }}.
Registered as {{ config('company.parent.name') }}, company no. {{ config('company.parent.registration_number') }}@if (config('company.parent.tax_pin')) | KRA PIN {{ config('company.parent.tax_pin') }}@endif

@if (config('company.registered_address')){{ collect([config('company.registered_address'), config('company.city'), config('company.country')])->filter()->implode(', ') }}
@endif
{{ config('app.url') }}@if (config('company.email.enquiries')) | {{ config('company.email.enquiries') }}@endif


This message was sent to {{ $submission->email }} because a request for proposal was
submitted from {{ parse_url(config('app.url'), PHP_URL_HOST) }} carrying that address. It is a
one-off acknowledgement, not a subscription. If it was not you, reply and tell
us, and we will delete the submission.
