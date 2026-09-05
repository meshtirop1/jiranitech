{{--
    Template T-14 — structured RFP intake.

    Track selection is step one and drives internal routing. The scope field enforces a
    minimum length because a technical lead reads it before replying, and a one-line
    brief cannot be routed to the right practice.
--}}

<x-layouts.app
    title="Request for Proposal"
    description="Structured enquiry intake for enterprise modernisation, new product builds, team augmentation and strategic advisory. Acknowledged within one business day."
>
    <x-site.page-header
        eyebrow="Request for Proposal"
        heading="Begin with a scoped conversation, not a sales call."
        standfirst="Our intake is structured so that the first response you receive is technical. Tell us where you are and we will route your enquiry to the engineering lead who owns that practice."
        :crumbs="['Contact' => route('contact.index'), 'Request for Proposal' => null]"
    />

    <section class="section" style="padding-top:2rem">
        <div class="shell">
            <div style="max-width:820px">
                @if ($errors->any())
                    <div class="errorsummary" style="margin-bottom:1.75rem" role="alert" tabindex="-1">
                        <h2>We could not submit that yet</h2>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('rfp.store') }}" class="form">
                    @csrf
                    <input type="hidden" name="source_page" value="{{ url()->previous() }}">

                    <fieldset class="fieldset">
                        <legend>Step 1 — Your position</legend>
                        <p class="field__hint" style="margin-top:-.35rem">
                            This determines who reads your enquiry first.
                        </p>

                        <div class="choice-grid">
                            @foreach ($tracks as $track)
                                <label class="choice">
                                    <input
                                        type="radio"
                                        name="track"
                                        value="{{ $track->value }}"
                                        @checked(old('track', $selectedTrack?->value) === $track->value)
                                        required
                                    >
                                    <span>
                                        <span class="choice__label">{{ $track->label() }}</span>
                                        <span class="choice__desc">{{ $track->description() }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        @error('track')
                            <p class="field__error">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend>Step 2 — Scope</legend>

                        <div class="field @error('scope_summary') field--invalid @enderror">
                            <label for="scope_summary">What are you trying to build, move or fix?</label>
                            <p class="field__hint">
                                A few sentences is enough. Systems involved, the constraint that matters most,
                                and what a successful outcome looks like.
                            </p>
                            <textarea id="scope_summary" name="scope_summary" required minlength="40" maxlength="4000">{{ old('scope_summary') }}</textarea>
                            @error('scope_summary')
                                <p class="field__error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="field">
                            <label>Disciplines you expect to be involved <span class="muted">(optional)</span></label>
                            <div class="choice-grid choice-grid--inline">
                                @foreach ($pillars as $pillar)
                                    <label class="choice">
                                        <input
                                            type="checkbox"
                                            name="service_interests[]"
                                            value="{{ $pillar->slug }}"
                                            @checked(in_array($pillar->slug, old('service_interests', []), true))
                                        >
                                        <span>
                                            <span class="choice__label">{{ $pillar->nav_title }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="field__row">
                            <div class="field">
                                <label for="budget_band">Indicative budget <span class="muted">(optional)</span></label>
                                <select id="budget_band" name="budget_band">
                                    <option value="">Prefer not to say</option>
                                    @foreach ($budgetBands as $band)
                                        <option value="{{ $band }}" @selected(old('budget_band') === $band)>{{ $band }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label for="timeline">Timeline <span class="muted">(optional)</span></label>
                                <select id="timeline" name="timeline">
                                    <option value="">Not yet determined</option>
                                    @foreach ($timelines as $timeline)
                                        <option value="{{ $timeline }}" @selected(old('timeline') === $timeline)>{{ $timeline }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="fieldset">
                        <legend>Step 3 — Contact</legend>

                        <div class="field__row">
                            <div class="field @error('organisation') field--invalid @enderror">
                                <label for="organisation">Organisation</label>
                                <input type="text" id="organisation" name="organisation" value="{{ old('organisation') }}" required maxlength="255">
                                @error('organisation')<p class="field__error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field @error('contact_name') field--invalid @enderror">
                                <label for="contact_name">Your name</label>
                                <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}" required maxlength="255">
                                @error('contact_name')<p class="field__error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="field__row">
                            <div class="field @error('email') field--invalid @enderror">
                                <label for="email">Business email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255">
                                @error('email')<p class="field__error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field">
                                <label for="role">Role <span class="muted">(optional)</span></label>
                                <input type="text" id="role" name="role" value="{{ old('role') }}" maxlength="255">
                            </div>
                        </div>

                        <div class="field__row">
                            <div class="field">
                                <label for="telephone">Telephone <span class="muted">(optional)</span></label>
                                <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}" maxlength="40">
                            </div>
                            <div class="field">
                                <label for="country">Country <span class="muted">(optional)</span></label>
                                <input type="text" id="country" name="country" value="{{ old('country') }}" maxlength="120">
                            </div>
                        </div>

                        <label class="choice">
                            <input type="checkbox" name="nda_required" value="1" @checked(old('nda_required'))>
                            <span>
                                <span class="choice__label">Send a mutual NDA before I disclose scope</span>
                                <span class="choice__desc">We will send one for signature before asking any further questions.</span>
                            </span>
                        </label>
                    </fieldset>

                    <div class="stack">
                        <div class="btn-row">
                            <button type="submit" class="btn btn--primary">Submit Request for Proposal</button>
                            <a class="btn btn--ghost" href="{{ route('contact.engagement-desk') }}">I would rather speak to someone</a>
                        </div>
                        <p class="small muted" style="max-width:66ch">
                            Acknowledged within {{ config('company.response.acknowledgement') }} and answered
                            substantively within {{ config('company.response.substantive') }}
                            ({{ config('company.timezone_label') }}). Submissions are treated as confidential and
                            processed under our
                            <a href="{{ route('legal.privacy') }}">Privacy Notice</a>.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
