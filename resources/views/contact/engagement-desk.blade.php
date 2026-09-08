<x-layouts.app
    title="Engagement Desk"
    description="General enquiries to Jiranisoko Tech Solutions, Eldoret, Kenya. Answered within one business day."
>
    <x-site.page-header
        eyebrow="Engagement desk"
        heading="Speak with someone before you write a brief."
        standfirst="Not every conversation starts with a requirement. If you want to understand whether we are the right firm before committing anything to writing, this is the route."
        :crumbs="['Contact' => route('contact.index'), 'Engagement desk' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell">
            <div style="max-width:820px" class="stack-lg">
                <div class="deflist">
                    @if (config('company.email.enquiries'))
                        <div class="deflist__row">
                            <dt>Email</dt>
                            <dd><a href="mailto:{{ config('company.email.enquiries') }}">{{ config('company.email.enquiries') }}</a></dd>
                        </div>
                    @endif
                    @if (config('company.telephone'))
                        <div class="deflist__row">
                            <dt>Telephone</dt>
                            <dd>{{ config('company.telephone') }}</dd>
                        </div>
                    @endif
                    @if (config('company.registered_address'))
                        <div class="deflist__row">
                            <dt>Registered office</dt>
                            <dd>{{ config('company.registered_address') }}, {{ config('company.city') }}, {{ config('company.country') }}</dd>
                        </div>
                    @endif
                    <div class="deflist__row">
                        <dt>Office hours</dt>
                        <dd>{{ config('company.office_hours') }}</dd>
                    </div>
                    <div class="deflist__row">
                        <dt>Response commitment</dt>
                        <dd>Acknowledged within {{ config('company.response.acknowledgement') }}, answered substantively within {{ config('company.response.substantive') }}.</dd>
                    </div>
                </div>

                <p class="prose-body measure">
                    If your enquiry is already a requirement, the structured intake will get you a technical
                    answer faster, because it routes directly to the practice that owns it.
                </p>

                <div class="btn-row">
                    <a class="btn btn--primary" href="{{ route('rfp.create') }}">Submit a Request for Proposal</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
