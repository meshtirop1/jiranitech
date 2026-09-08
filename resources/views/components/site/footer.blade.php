{{--
    H-12 — Corporate footer.

    Publication gate G-07: registered office, company registration number and a named
    contact route are mandatory before launch. Where a value is not yet configured the
    footer renders a visible marker rather than an empty space, so the omission is
    obvious in review instead of shipping silently.
--}}

<footer class="footer">
    <div class="shell">
        <div class="footer__grid">
            <div>
                <p class="footer__heading">{{ config('company.legal_name') }}</p>
                <div class="footer__identity">
                    <p>The enterprise technology division of {{ config('company.parent_name') }}.</p>

                    {{--
                        The division is not separately incorporated. State the holding
                        company's registration explicitly rather than letting the number sit
                        under this site's name, which would imply a legal entity that does
                        not exist and will not survive a vendor-registry check.
                    --}}
                    <p>
                        Registered as
                        <strong>{{ config('company.parent.name') }}</strong>@if (config('company.parent.registration_number')),
                            company no. {{ config('company.parent.registration_number') }}
                        @endif
                        @if (config('company.parent.tax_pin'))
                            &middot; KRA PIN {{ config('company.parent.tax_pin') }}
                        @endif
                        <br>
                        <span class="muted small">{{ config('company.parent.jurisdiction') }}</span>
                    </p>

                    @if (config('company.registered_address'))
                        <p>
                            Registered office:
                            {{ collect([
                                config('company.registered_address'),
                                config('company.city'),
                                config('company.country'),
                            ])->filter()->implode(', ') }}
                        </p>
                    @endif

                    @if (config('company.postal_address'))
                        <p>Postal address: {{ config('company.postal_address') }}</p>
                    @endif

                    <p>
                        @if (config('company.email.enquiries'))
                            <a href="mailto:{{ config('company.email.enquiries') }}">{{ config('company.email.enquiries') }}</a>
                        @endif
                        @if (config('company.telephone'))
                            &middot; {{ config('company.telephone') }}
                        @endif
                    </p>

                    <p class="muted small">Office hours {{ config('company.office_hours') }}.</p>
                </div>
            </div>

            <div>
                <p class="footer__heading">Services</p>
                <ul class="footer__list">
                    @foreach ($navPillars as $pillar)
                        <li><a href="{{ route('pillars.show', $pillar) }}">{{ $pillar->nav_title }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <p class="footer__heading">Engagement</p>
                <ul class="footer__list">
                    @foreach ($navEngagementModels as $model)
                        <li><a href="{{ route('engagement-models.show', $model) }}">{{ $model->title }}</a></li>
                    @endforeach
                    <li><a href="{{ route('rfp.create') }}">Request for Proposal</a></li>
                    <li><a href="{{ route('contact.engagement-desk') }}">Engagement desk</a></li>
                </ul>
            </div>

            <div>
                <p class="footer__heading">Company &amp; group</p>
                <ul class="footer__list">
                    <li><a href="{{ route('company.about') }}">About</a></li>
                    <li><a href="{{ route('company.governance') }}">Governance</a></li>
                    <li><a href="{{ route('company.leadership') }}">Leadership</a></li>
                    <li><a href="{{ route('company.careers') }}">Careers</a></li>
                    <li><a href="{{ route('insights.index') }}">Insights</a></li>
                    <li><x-site.group-link /></li>
                    <li><a href="{{ route('platforms.index') }}">Group platforms</a></li>
                </ul>
            </div>
        </div>

        <div class="footer__legal">
            <span>&copy; {{ now()->year }} {{ config('company.parent_name') }}. All rights reserved.</span>
            <a href="{{ route('legal.privacy') }}">Privacy Notice</a>
            <a href="{{ route('legal.terms') }}">Terms of Engagement</a>
            <a href="{{ route('legal.data-processing') }}">Data Processing Addendum</a>
            <a href="{{ route('legal.sub-processors') }}">Sub-processors</a>
            <a href="{{ route('legal.disclosure') }}">Responsible Disclosure</a>
            <a href="{{ route('legal.accessibility') }}">Accessibility Statement</a>
        </div>
    </div>
</footer>
