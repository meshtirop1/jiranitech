<x-layouts.app
    title="Accessibility Statement"
    description="Accessibility conformance target, known limitations and feedback route for the Jiranisoko Tech Solutions website."
>
    <x-site.page-header
        eyebrow="Accessibility"
        heading="Accessibility statement"
        standfirst="We target WCAG 2.2 Level AA for this website. This page states where we are against that target, including where we fall short."
        :crumbs="['Legal' => null, 'Accessibility statement' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell">
            <div style="max-width:820px" class="stack-lg">
                <div class="stack">
                    <h2 class="display-3">Conformance target</h2>
                    <p class="prose-body measure">
                        This site is built to conform to the Web Content Accessibility Guidelines version 2.2 at
                        Level AA. We consider that a requirement rather than an aspiration, particularly because
                        we deliver citizen-facing systems for public-sector clients under the same standard.
                    </p>
                </div>

                <div class="stack">
                    <h2 class="display-3">What is in place</h2>
                    <div class="deflist">
                        <div class="deflist__row"><dt>Keyboard</dt><dd>Every interactive control is reachable and operable by keyboard, with a visible focus indicator. The navigation menus close on Escape and return focus to their trigger.</dd></div>
                        <div class="deflist__row"><dt>Structure</dt><dd>Semantic headings in order, landmark regions, a skip link to the main content, and breadcrumb navigation on interior pages.</dd></div>
                        <div class="deflist__row"><dt>Colour</dt><dd>Text and interface colours are defined as tokens with contrast checked in both light and dark presentation. Colour is never the sole carrier of meaning; status is always also stated in words.</dd></div>
                        <div class="deflist__row"><dt>Motion</dt><dd>The site respects the reduced motion preference and contains no autoplaying or looping animation.</dd></div>
                        <div class="deflist__row"><dt>Images and charts</dt><dd>Diagrams carry text alternatives describing the information they convey, not merely their subject.</dd></div>
                        <div class="deflist__row"><dt>Forms</dt><dd>Every field is labelled, errors are described in text and summarised at the top of the form, and no error is signalled by colour alone.</dd></div>
                    </div>
                </div>

                <div class="note">
                    <span class="note__tag">How this was assessed</span>
                    <p>
                        The measures above were verified by our own testing, including automated contrast and
                        structure checks that run as part of our build and fail it on a regression. They have
                        <strong>not</strong> yet been verified by an independent audit or by testing with
                        assistive technology users, so this is a statement of the measures in place rather than
                        a certified conformance claim. We would rather say that than imply an audit we have not
                        had. Last reviewed
                        {{ \Illuminate\Support\Carbon::parse(config('legal.accessibility.reviewed_on'))->format('j F Y') }}.
                    </p>
                </div>

                <div class="stack">
                    <h2 class="display-3">Feedback</h2>
                    <p class="prose-body measure">
                        If you encounter a barrier on this site, tell us and we will fix it. Contact the
                        engagement desk with the page address and a description of what did not work. We aim to
                        respond within {{ config('company.response.acknowledgement') }} and will tell you our
                        intended remedy and timeline.
                    </p>
                    <p>
                        <a class="textlink" href="{{ route('contact.engagement-desk') }}">Engagement desk</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
