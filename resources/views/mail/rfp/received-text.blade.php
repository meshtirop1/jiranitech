NEW REQUEST FOR PROPOSAL — {{ $submission->reference }}

The sender has been acknowledged automatically and told to expect a substantive
reply within {{ config('company.response.substantive') }}. Replying to this message goes straight to them.

Organisation : {{ $submission->organisation }}
Contact      : {{ trim($submission->contact_name.($submission->role ? ', '.$submission->role : '')) }}
Email        : {{ $submission->email }}
@if ($submission->telephone)Telephone    : {{ $submission->telephone }}
@endif
Country      : {{ $submission->country }}
Track        : {{ $submission->track?->label() }}
Budget band  : {{ $submission->budget_band }}
Timeline     : {{ $submission->timeline }}
NDA          : {{ $submission->nda_required ? 'Yes' : 'No' }}

SCOPE AS WRITTEN

{{ $submission->scope_summary }}

Open it in the console: {{ route('admin.rfp.show', $submission) }}
