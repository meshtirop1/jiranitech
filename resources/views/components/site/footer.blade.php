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

                    <p>
                        Registered office:
                        @if (config('company.registered_address'))
                            {{ config('company.registered_address') }}
                        @else
                            <span class="pending">Gate G-07 — not configured</span>
                        @endif
                    </p>

                    <p>
                        Company registration:
                        @if (config('company.registration_number'))
                            {{ config('company.registration_number') }}
                        @else
                            <span class="pending">Gate G-07 — not configured</span>
                        @endif
                    </p>

                    <p>
                        @if (config('company.email.enquiries'))
                            <a href="mailto:{{ config('company.email.enquiries') }}">{{ config('company.email.enquiries') }}</a>
                        @else
                            <span class="pending">Gate G-07 — enquiries address not configured</span>
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
                    <li><a href="{{ config('company.parent_url') }}" rel="noopener">{{ config('company.parent_name') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="footer__legal">
            <span>&copy; {{ now()->year }} {{ config('company.parent_name') }}. All rights reserved.</span>
            <a href="{{ route('legal.privacy') }}">Privacy Notice</a>
            <a href="{{ route('legal.terms') }}">Terms of Engagement</a>
            <a href="{{ route('legal.data-processing') }}">Data Processing Addendum</a>
            <a href="{{ route('legal.disclosure') }}">Responsible Disclosure</a>
            <a href="{{ route('legal.accessibility') }}">Accessibility Statement</a>
        </div>
    </div>
</footer>
