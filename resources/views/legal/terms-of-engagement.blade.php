@php
    $supplier = config('company.parent.name');
    $registration = config('company.parent.registration_number');
    $division = config('company.legal_name');
    $t = config('legal.terms');
@endphp

<x-layouts.app
    title="Terms of Engagement"
    description="The standard contractual terms governing engagements with Jiranisoko Tech Solutions."
>
    <x-site.page-header
        eyebrow="Legal"
        heading="Terms of engagement"
        :crumbs="['Legal' => null, 'Terms of engagement' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell">
            <div style="max-width:820px" class="stack-lg">

                <div class="stack">
                    <p class="lede measure">
                        These are our standard terms. They set out how an engagement is formed, what each side
                        is responsible for, who owns what at the end, and what happens when something goes
                        wrong. They are written to be read before you sign rather than after something has
                        gone wrong.
                    </p>

                    <div class="deflist">
                        <div class="deflist__row">
                            <dt>Version</dt>
                            <dd>{{ $t['version'] }}, effective {{ \Illuminate\Support\Carbon::parse($t['effective_on'])->format('j F Y') }}. The version in force is the one identified in your statement of work.</dd>
                        </div>
                        <div class="deflist__row">
                            <dt>Contracting entity</dt>
                            <dd>
                                {{ $supplier }}@if ($registration) ({{ $registration }})@endif, incorporated in
                                the Republic of Kenya, acting through its {{ $division }} division. The division
                                is not a separate legal person and does not contract in its own name.
                            </dd>
                        </div>
                        <div class="deflist__row">
                            <dt>Order of precedence</dt>
                            <dd>The statement of work, then the <a class="textlink" href="{{ route('legal.data-processing') }}">data processing addendum</a> on any data protection question, then these terms, then any proposal. A statement of work varies these terms only where it says so expressly.</dd>
                        </div>
                    </div>
                </div>

                <div class="stack">
                    <h2 class="display-3">Terms</h2>

                    <div class="clauses">

                        <article class="clause">
                            <h3>How an engagement is formed</h3>
                            <ol>
                                <li>
                                    A proposal we issue is an invitation to discuss, not an offer capable of
                                    acceptance. An engagement begins when both parties sign a statement of work
                                    identifying the scope, the team, the charges and the service tier.
                                </li>
                                <li>
                                    Each statement of work is a separate contract incorporating these terms.
                                    Terminating one does not terminate another.
                                </li>
                                <li>
                                    Nothing said in a meeting, a proposal or a message varies a signed statement
                                    of work. Clause 3 is the only route to changing one.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>What we commit to</h3>
                            <ol>
                                <li>
                                    We will perform the services with the reasonable skill and care of a
                                    competent professional supplier in our field, and in accordance with the
                                    statement of work.
                                </li>
                                <li>
                                    <strong>Named people.</strong> The statement of work names the engineers on
                                    the engagement. We will not rotate a named person off it without your
                                    consent, except where they leave the firm, are unavailable through illness
                                    or leave, or you ask us to. Where we must replace someone, we provide an
                                    equivalently skilled replacement at our cost, including the cost of bringing
                                    them up to speed. Bench cover is our obligation, not your risk.
                                </li>
                                <li>
                                    <strong>Documentation.</strong> Work is documented as it is done, not
                                    retrospectively. A unit of work is not complete until what was done, why,
                                    and how to verify it has been written down and reviewed.
                                </li>
                                <li>
                                    <strong>Review.</strong> No engineer approves their own work. Every change
                                    is reviewed by someone other than its author before release.
                                </li>
                                <li>
                                    <strong>Reporting.</strong> You receive delivery reporting at the cadence in
                                    the statement of work, showing progress against the agreed scope, what is
                                    blocked, and by whom.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Scope, change and variation</h3>
                            <ol>
                                <li>
                                    Change is handled through a written variation rather than absorbed silently.
                                    Either party may propose one.
                                </li>
                                <li>
                                    A variation states what changes in scope, what it does to the timeline, and
                                    what it does to the charges. It takes effect when both parties sign it, and
                                    not before.
                                </li>
                                <li>
                                    We will tell you when a request falls outside the agreed scope rather than
                                    quietly absorbing it and reporting a delay later. Where we begin work on an
                                    unsigned variation at your written request, it is charged at the rates in
                                    the statement of work if the variation is not subsequently signed.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>What we need from you</h3>
                            <ol>
                                <li>
                                    Timely access to the people, systems, environments, credentials and
                                    information the engagement depends on, and a named person empowered to make
                                    decisions.
                                </li>
                                <li>
                                    Decisions within the timescales recorded in the statement of work. Where a
                                    decision or an access request is outstanding and is holding work up, we will
                                    record it as blocked, tell you, and stop billing time that cannot be worked.
                                </li>
                                <li>
                                    Accurate information. We are entitled to rely on what you tell us about your
                                    systems, your obligations and your data without independently verifying it.
                                </li>
                                <li>
                                    Where a delay is caused by something in this clause, timelines extend by the
                                    period of the delay and we may charge for team time reserved and not
                                    reasonably redeployable, having told you first.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Service levels and credits</h3>
                            <ol>
                                <li>
                                    Where the statement of work includes support or managed service, it names
                                    the tier. The tier framework published on this site describes the shape of
                                    those commitments; the targets that bind us are the ones written into your
                                    statement of work, and they are agreed against the composite service levels
                                    of the underlying infrastructure rather than quoted from a brochure.
                                </li>
                                <li>
                                    Availability is measured monthly, excluding planned maintenance notified in
                                    advance, your own systems and third-party services outside our control, and
                                    any period during which we are prevented from working by clause 4.
                                </li>
                                <li>
                                    Where the statement of work provides service credits and we miss an
                                    availability target, you may claim a credit against the following month's
                                    service charge. Credits are claimed in writing within thirty days of the end
                                    of the month concerned.
                                </li>
                                <li>
                                    Credits in any month are capped at
                                    <strong>{{ $t['service_credit_cap_percent'] }}%</strong> of that month's
                                    service charge, and are your exclusive financial remedy for a missed
                                    availability target. They are not a limitation on any other claim.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Charges, invoicing and payment</h3>
                            <ol>
                                <li>
                                    Charges are as stated in the statement of work, exclusive of value added tax
                                    and any other tax or duty, which are added where applicable.
                                </li>
                                <li>
                                    Invoices are payable within <strong>{{ $t['payment_days'] }} days</strong> of
                                    the invoice date. Where you dispute part of an invoice, pay the undisputed
                                    part on time and raise the disputed part in writing within fourteen days.
                                </li>
                                <li>
                                    We may charge interest on overdue sums at
                                    <strong>{{ $t['late_interest_percent'] }}% per month</strong>, and may
                                    suspend services on fourteen days' written notice where an undisputed
                                    invoice is more than thirty days overdue. Suspension does not relieve you of
                                    charges for the suspended period on a dedicated team engagement.
                                </li>
                                <li>
                                    Reasonable expenses agreed in advance are charged at cost, with receipts.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Intellectual property</h3>
                            <ol>
                                <li>
                                    <strong>Your material.</strong> Everything you provide remains yours. You
                                    grant us the licence necessary to perform the services and nothing more.
                                </li>
                                <li>
                                    <strong>Work product.</strong> All intellectual property in the deliverables
                                    we create specifically for you under a statement of work vests in you on
                                    payment of the charges to which it relates. Until then we hold it and grant
                                    you a licence to use it for the purposes of the engagement.
                                </li>
                                <li>
                                    <strong>Our background material.</strong> Tools, libraries, frameworks,
                                    methods and know-how that existed before the engagement or were developed
                                    independently of it remain ours. Where a deliverable incorporates any of it,
                                    we grant you a perpetual, irrevocable, worldwide, royalty-free,
                                    non-exclusive licence to use, modify and maintain it as part of that
                                    deliverable, including through another supplier.
                                </li>
                                <li>
                                    <strong>Open source.</strong> Third-party open source components remain
                                    under their own licences. We tell you what has been used and under what
                                    licence, and we do not introduce a component whose licence conflicts with
                                    the use you have told us you intend.
                                </li>
                                <li>
                                    We retain the right to use the general skills, experience and know-how
                                    gained on an engagement. This does not entitle us to use your confidential
                                    information or your material.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Source escrow</h3>
                            <ol>
                                <li>
                                    Source escrow is available on request. Where you ask for it, we agree the
                                    escrow agent, the deposit and verification schedule and the release events
                                    in a separate tripartite agreement with that agent.
                                </li>
                                <li>
                                    The agent's fees are payable by you unless the statement of work says
                                    otherwise. Escrow is not in place until that agreement is executed, and we
                                    will not represent it as in place before then.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Confidentiality</h3>
                            <ol>
                                <li>
                                    Each party keeps the other's confidential information confidential, uses it
                                    only for the engagement, and discloses it only to those who need it and are
                                    under equivalent obligations.
                                </li>
                                <li>
                                    This does not apply to information that is public other than through a
                                    breach, was already lawfully held, is independently developed, or must be
                                    disclosed by law — and where disclosure is compelled, the disclosing party
                                    tells the other first where it is lawful to do so.
                                </li>
                                <li>
                                    These obligations continue for five years after the engagement ends, and
                                    indefinitely for anything that is a trade secret.
                                </li>
                                <li>
                                    We will not name you as a client, or describe the engagement publicly,
                                    without your written consent.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Data protection</h3>
                            <ol>
                                <li>
                                    Where we process personal data on your behalf you are the controller and we
                                    are the processor, and the
                                    <a class="textlink" href="{{ route('legal.data-processing') }}">data processing addendum</a>
                                    applies. It forms part of every engagement in which personal data is
                                    processed and prevails over these terms on any data protection question.
                                </li>
                                <li>
                                    Each party complies with the Kenya Data Protection Act 2019 and, where it
                                    applies, the GDPR.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Warranties and defect correction</h3>
                            <ol>
                                <li>
                                    We warrant that the deliverables will conform in material respects to the
                                    specification in the statement of work for
                                    <strong>{{ $t['warranty_days'] }} days</strong> after acceptance.
                                </li>
                                <li>
                                    Where a deliverable does not, tell us and we will correct it at our cost
                                    within a reasonable period. That is your exclusive remedy for a defect, and
                                    it is not limited by clause 13.
                                </li>
                                <li>
                                    The warranty does not cover a defect caused by your modification of a
                                    deliverable, by use outside the specification, or by a third-party system we
                                    do not control.
                                </li>
                                <li>
                                    Except as expressly stated, all warranties implied by statute or common law
                                    are excluded to the extent the law permits. We do not warrant that software
                                    will be free of every defect, which is not a thing any honest supplier can
                                    promise.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Liability</h3>
                            <ol>
                                <li>
                                    Neither party excludes liability for death or personal injury caused by
                                    negligence, for fraud or fraudulent misrepresentation, or for anything else
                                    the law does not permit to be excluded.
                                </li>
                                <li>
                                    Neither party is liable for loss of profit, revenue, anticipated savings,
                                    goodwill or business opportunity, or for indirect or consequential loss,
                                    however arising.
                                </li>
                                <li>
                                    Each party's total liability under a statement of work is limited to the
                                    charges paid and payable under it in the
                                    <strong>{{ $t['liability_cap_months'] }} months</strong> before the event
                                    giving rise to the claim.
                                </li>
                                <li>
                                    The cap in clause 13.3 does not apply to your obligation to pay charges, to
                                    a breach of clause 9, or to a liability arising under the data processing
                                    addendum that data protection law does not permit to be limited.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Term, scale-down and termination</h3>
                            <ol>
                                <li>
                                    A dedicated engineering team engagement runs for a minimum term of
                                    <strong>{{ $t['minimum_term_days'] }} days</strong>. After that, you may
                                    reduce the team on <strong>{{ $t['scale_down_notice_days'] }} days'</strong>
                                    written notice.
                                </li>
                                <li>
                                    Either party may terminate a statement of work for convenience on
                                    <strong>{{ $t['termination_notice_days'] }} days'</strong> written notice,
                                    subject to any minimum term.
                                </li>
                                <li>
                                    Either party may terminate immediately where the other commits a material
                                    breach that is not remedied within thirty days of written notice, or becomes
                                    insolvent.
                                </li>
                                <li>
                                    On termination you pay for services performed and commitments reasonably
                                    incurred up to the termination date. Clause 15 applies however the
                                    engagement ends, including where we terminate for your breach.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Handover</h3>
                            <ol>
                                <li>
                                    Every engagement concludes with a documented handover. It is a contractual
                                    obligation, not a courtesy, and it is performed whether the engagement ends
                                    on completion, on notice or on breach.
                                </li>
                                <li>
                                    Handover comprises the architecture decision records, the runbooks needed to
                                    operate what we built, the source and its history, and the transfer of
                                    credentials and administrative access to you or to whoever you nominate.
                                </li>
                                <li>
                                    Handover is completed within thirty days of the end of the engagement.
                                    Reasonable assistance beyond that is available at the rates in the statement
                                    of work.
                                </li>
                                <li>
                                    We will not withhold handover material as leverage in a payment dispute,
                                    save that title in unpaid-for work product does not pass until it is paid
                                    for under clause 8.2.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Personnel</h3>
                            <ol>
                                <li>
                                    Neither party will solicit or employ a person involved in the engagement for
                                    the other party during it and for
                                    <strong>{{ $t['non_solicitation_months'] }} months</strong> afterwards,
                                    without the other's written consent.
                                </li>
                                <li>
                                    This does not prevent employment following a public advertisement not
                                    directed at that person.
                                </li>
                                <li>
                                    Our people are our employees or contractors. Nothing in an engagement makes
                                    them yours, and nothing creates a partnership, joint venture or employment
                                    relationship between the parties.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Matters outside reasonable control</h3>
                            <ol>
                                <li>
                                    Neither party is liable for a failure caused by something outside its
                                    reasonable control, including national infrastructure failure, natural
                                    disaster and civil disturbance. The affected party tells the other promptly
                                    and takes reasonable steps to mitigate.
                                </li>
                                <li>
                                    Where such an event continues for more than sixty days, either party may
                                    terminate the affected statement of work on written notice without further
                                    liability, and clause 15 applies.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>General</h3>
                            <ol>
                                <li>
                                    Neither party may assign a statement of work without the other's consent,
                                    which is not to be unreasonably withheld, save that either may assign to a
                                    group company or on a sale of its business.
                                </li>
                                <li>
                                    Notices are given in writing to the addresses in the statement of work.
                                    Notice by email is effective where receipt is acknowledged.
                                </li>
                                <li>
                                    A statement of work, these terms and the documents they incorporate are the
                                    entire agreement between the parties on their subject matter, and supersede
                                    anything said or written beforehand. Nothing limits liability for fraudulent
                                    misrepresentation.
                                </li>
                                <li>
                                    A failure to enforce a term is not a waiver of it. If any term is
                                    unenforceable, the rest continue and the term is applied to the extent it
                                    can be. No third party may enforce these terms.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Governing law and disputes</h3>
                            <ol>
                                <li>
                                    These terms and each statement of work are governed by the laws of the
                                    Republic of Kenya.
                                </li>
                                <li>
                                    The parties will first attempt to resolve any dispute by escalation: the
                                    engagement leads, then the Director of Delivery and your equivalent, then
                                    the Managing Director. Each stage has fourteen days.
                                </li>
                                <li>
                                    Failing that, the dispute is subject to the exclusive jurisdiction of the
                                    courts of Kenya. Nothing prevents either party seeking urgent injunctive
                                    relief at any time.
                                </li>
                            </ol>
                        </article>

                    </div>
                </div>

            </div>
        </div>
    </section>
</x-layouts.app>
