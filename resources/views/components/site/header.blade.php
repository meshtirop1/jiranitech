{{--
    H-01 — Utility bar and global header.

    The group affiliation line is the cheapest continuity proof on the site and sits
    above the fold on every page. It is not decorative; do not remove it to save space.

    Deviation from JTS-WEB-IA-001 section 2.2: the megamenu renders three columns over
    two rows rather than six columns. Each column carries a service list, and six
    parallel lists are unreadable at any realistic viewport width.
--}}

<div class="utility">
    <div class="shell utility__inner">
        <span>{{ config('company.division_line') }}</span>
        <div class="utility__links">
            @if (config('company.client_portal_url'))
                <a href="{{ config('company.client_portal_url') }}" rel="noopener">Client Portal</a>
            @endif
            <x-site.group-link label="Investor & Group" />
        </div>
    </div>
</div>

<header class="masthead" data-site-header>
    <div class="shell masthead__inner">
        {{-- Mark plus wordmark on one line, the way devcom.com sets theirs. The
             bars are the same three that make the favicon and the sharing card. --}}
        <a class="wordmark" href="{{ route('home') }}" aria-label="{{ config('company.legal_name') }} — home">
            <svg class="wordmark__mark" viewBox="0 0 64 64" aria-hidden="true" focusable="false">
                <rect x="18" y="16" width="18" height="7" fill="currentColor" opacity=".65"/>
                <rect x="18" y="27" width="27" height="7" fill="currentColor"/>
                <rect x="18" y="38" width="36" height="7" fill="currentColor"/>
            </svg>
            <span class="wordmark__primary">Jiranisoko</span>
            <span class="wordmark__secondary">Tech</span>
        </a>

        <nav class="nav" aria-label="Primary">
            <div class="nav__item" data-disclosure>
                <button type="button" class="nav__link" aria-expanded="false" aria-controls="menu-services" data-disclosure-trigger>
                    Services
                    <svg class="nav__caret" viewBox="0 0 10 10" aria-hidden="true"><path d="M2 4l3 3 3-3"/></svg>
                </button>
                <div class="panel panel--wide" id="menu-services" data-disclosure-panel hidden>
                    <div class="panel__grid">
                        @foreach ($navPillars as $pillar)
                            <div class="panel__col">
                                <span class="panel__num">Pillar {{ $pillar->reference() }}</span>
                                <a class="panel__title" href="{{ route('pillars.show', $pillar) }}">{{ $pillar->nav_title }}</a>
                                <p class="panel__desc">{{ $pillar->descriptor }}</p>
                                <ul class="panel__list">
                                    @foreach ($pillar->services as $service)
                                        <li><a href="{{ route('services.show', [$pillar, $service]) }}">{{ $service->title }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                    <div class="panel__foot">
                        <a class="textlink" href="{{ route('services.index') }}">View the complete service taxonomy</a>
                        <a class="textlink" href="{{ route('platforms.index') }}">Platforms we operate</a>
                    </div>
                </div>
            </div>

            <div class="nav__item" data-disclosure>
                <button type="button" class="nav__link" aria-expanded="false" aria-controls="menu-engagement" data-disclosure-trigger>
                    Engagement Models
                    <svg class="nav__caret" viewBox="0 0 10 10" aria-hidden="true"><path d="M2 4l3 3 3-3"/></svg>
                </button>
                <div class="panel panel--narrow" id="menu-engagement" data-disclosure-panel hidden>
                    <ul class="panel__linklist">
                        @foreach ($navEngagementModels as $model)
                            <li>
                                <a href="{{ route('engagement-models.show', $model) }}">
                                    {{ $model->title }}
                                    <span>{{ $model->reference }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="nav__item" data-disclosure>
                <button type="button" class="nav__link" aria-expanded="false" aria-controls="menu-industries" data-disclosure-trigger>
                    Industries
                    <svg class="nav__caret" viewBox="0 0 10 10" aria-hidden="true"><path d="M2 4l3 3 3-3"/></svg>
                </button>
                <div class="panel panel--narrow" id="menu-industries" data-disclosure-panel hidden>
                    <ul class="panel__linklist">
                        @foreach ($navIndustries as $industry)
                            <li><a href="{{ route('industries.show', $industry) }}">{{ $industry->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="nav__item" data-disclosure>
                <button type="button" class="nav__link" aria-expanded="false" aria-controls="menu-platforms" data-disclosure-trigger>
                    Platforms
                    <svg class="nav__caret" viewBox="0 0 10 10" aria-hidden="true"><path d="M2 4l3 3 3-3"/></svg>
                </button>
                <div class="panel panel--narrow" id="menu-platforms" data-disclosure-panel hidden>
                    <ul class="panel__linklist">
                        @foreach ($navPlatforms as $platform)
                            <li><a href="{{ route('platforms.show', $platform) }}">{{ $platform->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="nav__item">
                <a class="nav__link" href="{{ route('insights.index') }}" @if (request()->routeIs('insights.*')) aria-current="page" @endif>Insights</a>
            </div>

            <div class="nav__item" data-disclosure>
                <button type="button" class="nav__link" aria-expanded="false" aria-controls="menu-company" data-disclosure-trigger>
                    Company
                    <svg class="nav__caret" viewBox="0 0 10 10" aria-hidden="true"><path d="M2 4l3 3 3-3"/></svg>
                </button>
                <div class="panel panel--narrow" id="menu-company" data-disclosure-panel hidden>
                    <ul class="panel__linklist">
                        <li><a href="{{ route('company.about') }}">About<span>Mandate and institutional heritage</span></a></li>
                        <li><a href="{{ route('company.governance') }}">Governance<span>Security, compliance and service assurance</span></a></li>
                        <li><a href="{{ route('company.leadership') }}">Leadership</a></li>
                        <li><a href="{{ route('company.delivery-model') }}">Delivery model<span>Methodology and SLA framework</span></a></li>
                        <li><a href="{{ route('company.careers') }}">Careers</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="header-actions">
            <a class="btn btn--primary" href="{{ route('rfp.create') }}">Request Proposal</a>
            <button type="button" class="btn btn--ghost nav-toggle" aria-expanded="false" aria-controls="site-drawer" data-drawer-trigger>
                Menu
            </button>
        </div>
    </div>

    <div class="drawer" id="site-drawer" data-drawer hidden>
        <div class="shell">
            <div class="drawer__group">
                <details>
                    <summary class="drawer__summary">Services <span aria-hidden="true">+</span></summary>
                    <ul class="panel__linklist">
                        @foreach ($navPillars as $pillar)
                            <li><a href="{{ route('pillars.show', $pillar) }}">{{ $pillar->nav_title }}<span>{{ $pillar->services->count() }} service lines</span></a></li>
                        @endforeach
                        <li><a href="{{ route('services.index') }}">Complete service taxonomy</a></li>
                    </ul>
                </details>
            </div>
            <div class="drawer__group">
                <details>
                    <summary class="drawer__summary">Engagement Models <span aria-hidden="true">+</span></summary>
                    <ul class="panel__linklist">
                        @foreach ($navEngagementModels as $model)
                            <li><a href="{{ route('engagement-models.show', $model) }}">{{ $model->title }}</a></li>
                        @endforeach
                    </ul>
                </details>
            </div>
            <div class="drawer__group">
                <details>
                    <summary class="drawer__summary">Industries <span aria-hidden="true">+</span></summary>
                    <ul class="panel__linklist">
                        @foreach ($navIndustries as $industry)
                            <li><a href="{{ route('industries.show', $industry) }}">{{ $industry->title }}</a></li>
                        @endforeach
                    </ul>
                </details>
            </div>
            <div class="drawer__group">
                <details>
                    <summary class="drawer__summary">Company <span aria-hidden="true">+</span></summary>
                    <ul class="panel__linklist">
                        <li><a href="{{ route('company.about') }}">About</a></li>
                        <li><a href="{{ route('company.governance') }}">Governance</a></li>
                        <li><a href="{{ route('company.leadership') }}">Leadership</a></li>
                        <li><a href="{{ route('company.delivery-model') }}">Delivery model</a></li>
                        <li><a href="{{ route('company.careers') }}">Careers</a></li>
                    </ul>
                </details>
            </div>
            <div class="drawer__group">
                <a class="drawer__link" href="{{ route('platforms.index') }}">Platforms</a>
            </div>
            <div class="drawer__group">
                <a class="drawer__link" href="{{ route('insights.index') }}">Insights</a>
            </div>
            <div class="drawer__group" style="border-top:0;padding-top:1.25rem">
                <a class="btn btn--primary" style="width:100%" href="{{ route('rfp.create') }}">Request Proposal</a>
            </div>
        </div>
    </div>
</header>
