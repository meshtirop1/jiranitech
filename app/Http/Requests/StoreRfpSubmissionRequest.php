<?php

namespace App\Http\Requests;

use App\Enums\RfpTrack;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRfpSubmissionRequest extends FormRequest
{
    /** @var array<int, string> */
    public const BUDGET_BANDS = [
        'Under USD 50,000',
        'USD 50,000 – 150,000',
        'USD 150,000 – 500,000',
        'Over USD 500,000',
        'Not yet determined',
    ];

    /** @var array<int, string> */
    public const TIMELINES = [
        'Immediate — within 30 days',
        'This quarter',
        'Next two quarters',
        'Exploratory — no fixed date',
    ];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'track' => ['required', Rule::enum(RfpTrack::class)],
            'organisation' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            // RFC validation only, deliberately without the `dns` check. A live DNS
            // lookup on every submission makes acceptance depend on network conditions
            // at the moment of posting, and a transient failure would reject a genuine
            // enquiry with a message telling the sender their address is wrong. On a
            // form whose purpose is capturing enterprise RFPs, losing a real enquiry
            // costs far more than accepting an occasional typo, which surfaces anyway
            // when the reply bounces.
            'email' => ['required', 'email:rfc', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:120'],
            'budget_band' => ['nullable', 'string', Rule::in(self::BUDGET_BANDS)],
            'timeline' => ['nullable', 'string', Rule::in(self::TIMELINES)],
            'scope_summary' => ['required', 'string', 'min:40', 'max:4000'],
            'service_interests' => ['nullable', 'array', 'max:6'],
            'service_interests.*' => ['string', 'exists:pillars,slug'],
            'nda_required' => ['boolean'],
            'source_page' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'scope_summary.min' => 'Please give us at least a couple of sentences on the scope. A technical lead reads this before replying, and a one-line brief cannot be routed accurately.',
            'track.required' => 'Select the position that best describes your requirement so we can route the enquiry to the right practice.',
            'email.email' => 'Enter a working business email address. We reply to this address, so a typo means you will not hear from us.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nda_required' => $this->boolean('nda_required'),
        ]);
    }
}
