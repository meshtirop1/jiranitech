@php
    $processor = config('company.parent.name');
    $registration = config('company.parent.registration_number');
    $division = config('company.legal_name');
    $privacy = config('company.email.privacy');
    $notice = config('sub_processors.notice_days');
    $version = config('legal.dpa.version');
    $effective = config('legal.dpa.effective_on');
@endphp

<x-layouts.app
    title="Data Processing Addendum"
    description="The data processing terms attaching to every Jiranisoko Tech Solutions engagement, meeting GDPR Article 28(3) and the Kenya Data Protection Act 2019."
>
    <x-site.page-header
        eyebrow="Legal"
        heading="Data processing addendum"
        :crumbs="['Legal' => null, 'Data processing addendum' => null]"
    />

    <section class="section" style="padding-top:2.5rem">
        <div class="shell">
            <div style="max-width:820px" class="stack-lg">

                <div class="note note--gate">
                    <span class="note__tag">Draft {{ $version }} — not yet executed</span>
                    <p>
                        This instrument is drafted and complete in its terms. It has <strong>not</strong> been
                        reviewed by counsel or signed off by the Data Protection Officer, and no engagement is
                        governed by it until it has been. Publication gate G-06 stays open until that review is
                        recorded. Read what follows as the position we intend to be bound to, not as a
                        representation that we are already bound to it.
                    </p>
                </div>

                <div class="stack">
                    <p class="lede measure">
                        This addendum governs our processing of personal data on a client's behalf. It attaches
                        to the engagement contract and takes precedence over it on any question of data
                        protection. Where an engagement involves no personal data at all, Annex 1 records that
                        and the operative clauses have nothing to bite on.
                    </p>

                    <div class="deflist">
                        <div class="deflist__row">
                            <dt>Version</dt>
                            <dd>{{ $version }}, effective {{ \Illuminate\Support\Carbon::parse($effective)->format('j F Y') }}. Superseded versions are kept and quoted by version number in each engagement.</dd>
                        </div>
                        <div class="deflist__row">
                            <dt>Contracting entity</dt>
                            <dd>
                                {{ $processor }}@if ($registration) ({{ $registration }})@endif, a company incorporated in the
                                Republic of Kenya, acting through its {{ $division }} division. The division is
                                not a separate legal person and does not contract in its own name.
                            </dd>
                        </div>
                        <div class="deflist__row">
                            <dt>Applies to</dt>
                            <dd>Every engagement in which we process personal data for which the client is the controller. It does not govern personal data we collect for our own purposes, which is covered by the <a class="textlink" href="{{ route('legal.privacy') }}">privacy notice</a>.</dd>
                        </div>
                    </div>
                </div>

                {{-- ------------------------------------------------ operative --- --}}

                <div class="stack">
                    <h2 class="display-3">Terms</h2>

                    <div class="clauses">

                        <article class="clause">
                            <h3>Definitions and interpretation</h3>
                            <ol>
                                <li>
                                    <strong>Data Protection Law</strong> means the Kenya Data Protection Act 2019
                                    and the regulations made under it, including the Data Protection (General)
                                    Regulations 2021; and, where the processing falls within its scope, Regulation
                                    (EU) 2016/679 (the <strong>GDPR</strong>) and the equivalent United Kingdom
                                    legislation. Where two regimes apply to the same processing, the stricter
                                    obligation governs.
                                </li>
                                <li>
                                    <strong>Controller</strong>, <strong>Processor</strong>,
                                    <strong>Data Subject</strong>, <strong>Personal Data</strong>,
                                    <strong>Processing</strong> and <strong>Personal Data Breach</strong> carry the
                                    meanings given to them in Data Protection Law.
                                </li>
                                <li>
                                    <strong>Controller</strong> in this addendum means the client named in the
                                    engagement contract. <strong>Processor</strong> means the contracting entity
                                    named above. <strong>Sub-processor</strong> means any third party engaged by
                                    the Processor to process Personal Data on the Controller's behalf.
                                </li>
                                <li>
                                    <strong>Commissioner</strong> means the Data Commissioner appointed under the
                                    Kenya Data Protection Act 2019, and <strong>Supervisory Authority</strong>
                                    means the Commissioner or any other competent authority for the processing.
                                </li>
                                <li>
                                    <strong>Annex</strong> means an annex to this addendum. Annexes 1 and 2 are
                                    completed for each engagement and form part of it. Annex 3 is the register
                                    published at <a class="textlink" href="{{ route('legal.sub-processors') }}">{{ route('legal.sub-processors') }}</a>
                                    as it stands from time to time.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Roles of the parties</h3>
                            <ol>
                                <li>
                                    The Controller determines the purposes and means of the Processing. The
                                    Processor processes only on the Controller's behalf. Nothing in an engagement
                                    makes the Processor a controller of the Personal Data, and the Processor does
                                    not process it for any purpose of its own.
                                </li>
                                <li>
                                    The Controller warrants that it has a lawful basis for the Processing, that
                                    the required information has been given to Data Subjects, and that its
                                    instructions do not require the Processor to act unlawfully.
                                </li>
                                <li>
                                    The Processor does not aggregate, anonymise, derive statistics from, or train
                                    any model on the Controller's Personal Data for its own benefit. Where such
                                    processing is wanted, it is an instruction recorded in Annex 1 and not an
                                    assumed liberty.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Subject matter, duration, nature and purpose</h3>
                            <ol>
                                <li>
                                    The subject matter, duration, nature and purpose of the Processing, the types
                                    of Personal Data and the categories of Data Subject are set out in
                                    <strong>Annex 1</strong>, completed for each engagement before Processing
                                    begins.
                                </li>
                                <li>
                                    Processing continues for the term of the engagement and for the period in
                                    clause 10 that follows its end. It does not continue past that period for any
                                    reason not stated in this addendum.
                                </li>
                                <li>
                                    An engagement whose Annex 1 records that no Personal Data is processed is
                                    governed by this addendum only to the extent that the position later changes.
                                    A change is a variation to Annex 1, made in writing before the first
                                    Processing.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Processing on documented instructions</h3>
                            <ol>
                                <li>
                                    The Processor processes Personal Data only on the Controller's documented
                                    instructions, including as to transfers, unless required to do otherwise by a
                                    law to which it is subject. Where a law requires it, the Processor informs the
                                    Controller of that requirement before Processing, unless the law forbids that
                                    notice on important grounds of public interest.
                                </li>
                                <li>
                                    This addendum, the engagement contract and Annex 1 are the Controller's
                                    initial documented instructions. Further instructions are given in writing to
                                    the engagement's named contact and are recorded against the engagement.
                                </li>
                                <li>
                                    The Processor informs the Controller without delay if, in its opinion, an
                                    instruction infringes Data Protection Law, and may suspend the affected
                                    Processing until the instruction is confirmed, varied or withdrawn. Suspension
                                    on this ground is not a breach of the engagement contract.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Confidentiality of personnel</h3>
                            <ol>
                                <li>
                                    Access to Personal Data is limited to personnel who need it to deliver the
                                    engagement. Access is granted by named post, reviewed when a person's role
                                    changes, and withdrawn on the day an engagement or an employment ends.
                                </li>
                                <li>
                                    Every person authorised to process Personal Data is bound by a written
                                    confidentiality obligation that survives the end of their engagement or
                                    employment. Where a statutory duty of confidence already binds them, that duty
                                    is in addition to and not in place of the written one.
                                </li>
                                <li>
                                    Personnel are instructed on their obligations under this addendum before they
                                    are given access, and the instruction is recorded.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Security of processing</h3>
                            <ol>
                                <li>
                                    The Processor implements the technical and organisational measures set out in
                                    <strong>Annex 2</strong>, having regard to the state of the art, the cost of
                                    implementation, and the nature, scope, context and purposes of the Processing
                                    as against the risk to Data Subjects.
                                </li>
                                <li>
                                    Annex 2 states the measures actually in operation. It is not a statement of
                                    intent, and a measure is removed from it on the day it stops being true rather
                                    than at the next review.
                                </li>
                                <li>
                                    The Processor may change a measure, but not so as to lower the overall level
                                    of security. A material change to Annex 2 is notified to the Controller in the
                                    same way as a change of Sub-processor.
                                </li>
                                <li>
                                    Where the engagement requires a control that Annex 2 does not list, it is
                                    recorded in Annex 1 as a specific obligation and priced accordingly. The
                                    Controller should not assume a control merely because it is common.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Sub-processors</h3>
                            <ol>
                                <li>
                                    The Controller gives general written authorisation for the Processor to engage
                                    the Sub-processors listed as engaged in <strong>Annex 3</strong> at the date
                                    the addendum is signed.
                                </li>
                                <li>
                                    A party listed in Annex 3 as conditional is not authorised by that listing. It
                                    is engaged only where the Controller authorises it in writing for that
                                    engagement, and the authorisation is recorded in Annex 1.
                                </li>
                                <li>
                                    The Processor imposes on every Sub-processor, by written contract, data
                                    protection obligations no less protective than those in this addendum. The
                                    Processor remains fully liable to the Controller for a Sub-processor's
                                    performance as if it were its own.
                                </li>
                                <li>
                                    The Processor gives the Controller at least <strong>{{ $notice }} days'</strong>
                                    written notice before a new Sub-processor begins Processing, or before an
                                    existing one takes on a materially different role. Within that period the
                                    Controller may object on reasonable data protection grounds.
                                </li>
                                <li>
                                    On an objection the parties work in good faith to find an alternative. If none
                                    is available within a reasonable period, the Controller may terminate the
                                    affected part of the engagement without penalty, on notice, and clause 10
                                    applies to the Personal Data. The Processor does not engage the objected-to
                                    Sub-processor for that Controller's Personal Data in the meantime.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Transfers outside Kenya</h3>
                            <ol>
                                <li>
                                    Personal Data is processed in Kenya unless Annex 1 or Annex 3 records
                                    otherwise. The Processor does not transfer Personal Data out of Kenya without
                                    the Controller's documented instruction and a lawful transfer mechanism in
                                    place before the transfer.
                                </li>
                                <li>
                                    Where a transfer is instructed, it is made on one of: a finding by the
                                    Commissioner that the destination affords adequate protection; appropriate
                                    safeguards recognised by the Kenya Data Protection Act 2019, including binding
                                    contractual clauses; or another basis permitted by that Act. Where the GDPR
                                    also applies, the transfer additionally relies on a mechanism under Chapter V
                                    of the GDPR, ordinarily the European Commission's standard contractual
                                    clauses.
                                </li>
                                <li>
                                    The mechanism relied upon for each Sub-processor is stated in Annex 3. Where
                                    an entry records that a location is not yet confirmed in writing, the
                                    Processor does not treat the favourable answer as given: it obtains
                                    confirmation, and if the answer places Processing outside Kenya it notifies
                                    the Controller under clause 7.4 before Processing continues.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Assistance to the controller</h3>
                            <ol>
                                <li>
                                    <strong>Data subject rights.</strong> Taking account of the nature of the
                                    Processing, the Processor assists the Controller by appropriate technical and
                                    organisational measures, so far as possible, to respond to requests to
                                    exercise Data Subject rights. Where the Processor receives such a request
                                    directly, it does not respond to it itself but forwards it to the Controller
                                    without undue delay and in any event within
                                    <strong>three business days</strong>.
                                </li>
                                <li>
                                    <strong>Personal data breach.</strong> The Processor notifies the Controller
                                    without undue delay and in any event within <strong>twenty-four hours</strong>
                                    of becoming aware of a Personal Data Breach affecting the Controller's
                                    Personal Data. The notification states, so far as known:
                                    <ol>
                                        <li>the nature of the breach, including the categories and approximate number of Data Subjects and records concerned;</li>
                                        <li>the likely consequences;</li>
                                        <li>the measures taken or proposed to address it, including to mitigate its effects; and</li>
                                        <li>a contact point for further information.</li>
                                    </ol>
                                </li>
                                <li>
                                    Information not available at the time of notification is provided in phases as
                                    it is established, without further delay. The Processor does not withhold an
                                    initial notification because the picture is incomplete, and it assists the
                                    Controller in meeting the Controller's own deadline for notifying the
                                    Commissioner, which the Kenya Data Protection Act 2019 sets at seventy-two
                                    hours from awareness.
                                </li>
                                <li>
                                    <strong>Impact assessments.</strong> The Processor assists the Controller with
                                    data protection impact assessments and with any prior consultation with a
                                    Supervisory Authority, to the extent the assistance relates to Processing by
                                    the Processor and taking account of the information available to it.
                                </li>
                                <li>
                                    Assistance under this clause is provided at no additional charge where it is
                                    proportionate to the engagement. Where a request is repetitive or requires
                                    material engineering effort, the Processor may charge at the rates in the
                                    engagement contract, having first told the Controller and agreed the scope.
                                    Cost is never a reason to delay a breach notification under clause 9.2.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Deletion or return</h3>
                            <ol>
                                <li>
                                    At the Controller's election, the Processor deletes or returns all Personal
                                    Data at the end of the engagement, and deletes existing copies, unless a law
                                    to which the Processor is subject requires it to be kept. Where retention is
                                    required by law, the Processor tells the Controller what is retained, on what
                                    basis, and for how long.
                                </li>
                                <li>
                                    The Controller states its election in writing before the end of the
                                    engagement. Absent an election, the Processor returns the Personal Data in a
                                    structured, commonly used, machine-readable format and then deletes it.
                                </li>
                                <li>
                                    Deletion is completed within <strong>thirty days</strong> of the end of the
                                    engagement or of the return, whichever is later. The Processor issues a
                                    written confirmation of deletion on request.
                                </li>
                                <li>
                                    Personal Data held in backups is not individually deleted. It is isolated from
                                    active Processing, protected by the measures in Annex 2, and deleted when the
                                    backup expires on its ordinary cycle. The Processor states that cycle on
                                    request.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Records, audit and inspection</h3>
                            <ol>
                                <li>
                                    The Processor keeps a record of the categories of Processing carried out on
                                    the Controller's behalf, as required of processors by Data Protection Law, and
                                    makes it available to the Controller on request.
                                </li>
                                <li>
                                    The Processor makes available to the Controller all information necessary to
                                    demonstrate compliance with this addendum, and allows for and contributes to
                                    audits, including inspections, conducted by the Controller or an auditor it
                                    mandates.
                                </li>
                                <li>
                                    An audit may be conducted once in any twelve-month period on
                                    <strong>thirty days'</strong> written notice, during business hours, without
                                    unreasonable disruption, and subject to confidentiality obligations. This
                                    limit does not apply to an audit following a Personal Data Breach, or where a
                                    Supervisory Authority requires one, and neither is subject to the notice
                                    period in this clause.
                                </li>
                                <li>
                                    The auditor may not be a competitor of the Processor. Access is not given to
                                    another client's data, to personnel data unrelated to the engagement, or to
                                    material subject to legal professional privilege.
                                </li>
                                <li>
                                    Each party bears its own costs, save that the Controller bears the Processor's
                                    reasonable costs of an audit it requests more than once in a twelve-month
                                    period.
                                </li>
                            </ol>
                        </article>

                        <article class="clause">
                            <h3>Liability, term and precedence</h3>
                            <ol>
                                <li>
                                    This addendum begins when the engagement contract begins, or on the day it is
                                    signed if later, and continues for as long as the Processor holds the
                                    Controller's Personal Data. Clauses 5, 10, 11 and this clause survive its
                                    termination.
                                </li>
                                <li>
                                    Liability under this addendum is subject to the limitations and exclusions in
                                    the engagement contract, save that nothing limits a liability that Data
                                    Protection Law does not permit to be limited, including liability to a Data
                                    Subject.
                                </li>
                                <li>
                                    Where this addendum conflicts with the engagement contract or with any other
                                    agreement between the parties, this addendum prevails on data protection
                                    matters. Where it conflicts with a Sub-processor's terms, this addendum
                                    prevails as between the Controller and the Processor.
                                </li>
                                <li>
                                    This addendum is governed by the laws of the Republic of Kenya. Disputes are
                                    resolved by the procedure in the engagement contract, and failing one, by the
                                    courts of Kenya. Nothing here affects a Data Subject's right to complain to
                                    the Commissioner or to any other Supervisory Authority.
                                </li>
                            </ol>
                        </article>

                    </div>
                </div>

                {{-- --------------------------------------------------- annex 1 --- --}}

                <div class="stack">
                    <h2 class="display-3">Annex 1 — Details of the processing</h2>
                    <p class="prose-body measure">
                        Completed for each engagement before Processing begins, and varied in writing if the
                        position changes. An engagement that has not completed this annex has not been
                        instructed, and the Processor does not begin.
                    </p>

                    <div class="tablewrap">
                        <table class="data">
                            <thead>
                                <tr><th scope="col">Item</th><th scope="col">To be recorded for the engagement</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Subject matter</td>
                                    <td>The system or service being built, operated or supported, identified by the engagement reference.</td>
                                </tr>
                                <tr>
                                    <td>Duration</td>
                                    <td>The engagement term, plus the deletion period in clause 10.3.</td>
                                </tr>
                                <tr>
                                    <td>Nature and purpose</td>
                                    <td>What is actually done to the data — for example storage and hosting, migration from a legacy system, support access to a production database, or processing under test with production data. Each carries a different risk and is named separately.</td>
                                </tr>
                                <tr>
                                    <td>Categories of personal data</td>
                                    <td>Listed by field group, not by system. Any special category data, and any data relating to children, is called out expressly.</td>
                                </tr>
                                <tr>
                                    <td>Categories of data subjects</td>
                                    <td>For example the Controller's customers, members, patients, employees or citizens dealing with a public body.</td>
                                </tr>
                                <tr>
                                    <td>Processing locations</td>
                                    <td>Where Processing takes place, and any location outside Kenya with the clause 8 mechanism relied upon.</td>
                                </tr>
                                <tr>
                                    <td>Conditional sub-processors authorised</td>
                                    <td>Any party from the conditional list in Annex 3 that the Controller authorises for this engagement, in writing.</td>
                                </tr>
                                <tr>
                                    <td>Named contacts</td>
                                    <td>The Controller's contact for instructions and for breach notification, and the Processor's engagement lead.</td>
                                </tr>
                                <tr>
                                    <td>Additional controls</td>
                                    <td>Any control required beyond Annex 2, under clause 6.4.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- --------------------------------------------------- annex 2 --- --}}

                <div class="stack">
                    <h2 class="display-3">Annex 2 — Technical and organisational measures</h2>
                    <p class="prose-body measure">
                        These are the measures in operation, stated specifically as Article 32 requires. A
                        measure we do not operate is not listed, however ordinary it may be elsewhere — a
                        controller assessing us is better served by a short accurate list than a long
                        aspirational one. Measures marked <em>engagement-dependent</em> exist where the
                        engagement's own infrastructure provides them and are recorded in Annex 1.
                    </p>

                    <div class="tablewrap">
                        <table class="data">
                            <thead>
                                <tr><th scope="col">Area</th><th scope="col">Measure in operation</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Transport security</td>
                                    <td>All connections to our systems are served over TLS. Plain HTTP is redirected at the web server and no service is offered on an unencrypted port.</td>
                                </tr>
                                <tr>
                                    <td>Authentication</td>
                                    <td>Account passwords are stored only as bcrypt hashes, never reversibly. Sign-in is rate limited. There is no self-registration on any internal system: accounts are created by named leadership posts.</td>
                                </tr>
                                <tr>
                                    <td>Access control</td>
                                    <td>Role-based access on the principle of least privilege, enforced in application code rather than by convention, and covered by an automated test suite that fails the build if a role gains authority it should not have.</td>
                                </tr>
                                <tr>
                                    <td>Segregation of duties</td>
                                    <td>No person may approve their own work. Information security review can halt a release independently of delivery. The Data Protection Officer sits outside delivery, holds no delivery work, and cannot be assigned any.</td>
                                </tr>
                                <tr>
                                    <td>Auditability</td>
                                    <td>State changes to delivery records are written to an append-only activity trail. The application refuses to update or delete an entry, so the record of who accepted what, and when, cannot be edited after the fact.</td>
                                </tr>
                                <tr>
                                    <td>Change control</td>
                                    <td>All source is held in version control. Every change is attributable to a named author, documented before review, and reviewed by someone other than its author before release.</td>
                                </tr>
                                <tr>
                                    <td>Third-party exposure</td>
                                    <td>Our own web properties load no third-party analytics, advertising, content-delivery or error-tracking service. Fonts and assets are served from our own domain, so a visitor's IP address is not disclosed to any third party by these sites as built.</td>
                                </tr>
                                <tr>
                                    <td>Mail authentication</td>
                                    <td>SPF, DKIM and DMARC are published for our sending domain, so mail purporting to come from us can be verified by the receiving system.</td>
                                </tr>
                                <tr>
                                    <td>Backups</td>
                                    <td>A database backup is taken at each release and retained on the host. Backups are covered by clause 10.4. Backup encryption is engagement-dependent and recorded in Annex 1 where the engagement requires it.</td>
                                </tr>
                                <tr>
                                    <td>Personnel</td>
                                    <td>Written confidentiality obligations surviving the end of employment; access granted by named post and withdrawn on the last day; instruction on these obligations recorded before access is given.</td>
                                </tr>
                                <tr>
                                    <td>Data minimisation in support</td>
                                    <td>Production data is not copied to development or test environments unless Annex 1 records it as instructed, with the controls that apply to it.</td>
                                </tr>
                                <tr>
                                    <td>Encryption at rest</td>
                                    <td><em>Engagement-dependent.</em> Where the engagement's infrastructure provides encryption at rest it is used and recorded in Annex 1. We do not represent it as a general measure across all engagements.</td>
                                </tr>
                                <tr>
                                    <td>Multi-factor authentication</td>
                                    <td><em>Not yet in operation</em> on our own internal systems. It is listed here because a controller is entitled to know what is absent, and because clause 6.2 requires this annex to state the position as it is.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- --------------------------------------------------- annex 3 --- --}}

                <div class="stack">
                    <h2 class="display-3">Annex 3 — Sub-processors</h2>
                    <p class="prose-body measure">
                        Annex 3 is the register published at
                        <a class="textlink" href="{{ route('legal.sub-processors') }}">{{ route('legal.sub-processors') }}</a>,
                        as it stands from time to time. It is maintained separately so that a change can be
                        published and notified under clause 7.4 without reopening the signed addendum. The
                        register carries its own version number and change log, and the version in force at
                        signature is recorded in Annex 1.
                    </p>
                </div>

                {{-- ------------------------------------------------- execution --- --}}

                <div class="stack">
                    <h2 class="display-3">Execution</h2>
                    <p class="prose-body measure">
                        This addendum takes effect on signature by both parties, or on the date the engagement
                        contract incorporating it takes effect, whichever is earlier. A copy for signature is
                        available from the engagement desk.
                        @if ($privacy)
                            Requests and questions about these terms go to
                            <a class="textlink" href="mailto:{{ $privacy }}">{{ $privacy }}</a>.
                        @endif
                    </p>

                    <div class="execution">
                        <div class="execution__party">
                            <h4>For the Processor</h4>
                            <div class="execution__field">Signature</div>
                            <div class="execution__field">Name</div>
                            <div class="execution__field">Post</div>
                            <div class="execution__field">Date</div>
                            <p class="prose-body" style="font-size:.82rem">
                                {{ $processor }}@if ($registration) ({{ $registration }})@endif, acting through
                                its {{ $division }} division.
                            </p>
                        </div>
                        <div class="execution__party">
                            <h4>For the Controller</h4>
                            <div class="execution__field">Signature</div>
                            <div class="execution__field">Name</div>
                            <div class="execution__field">Post</div>
                            <div class="execution__field">Date</div>
                            <p class="prose-body" style="font-size:.82rem">
                                The client named in the engagement contract, as controller of the Personal Data
                                described in Annex 1.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="note">
                    <span class="note__tag">Before this is relied upon</span>
                    <p>
                        Three things sit outside a drafter's reach and must be settled by counsel and the Data
                        Protection Officer: whether the contracting entity is registered with the Office of the
                        Data Protection Commissioner as a data processor, which the Act requires of processors
                        meeting its thresholds; whether the liability position in clause 12.2 matches the cap in
                        the engagement contract, which is not yet drafted; and confirmation in writing of where
                        our hosting provider's serving facility physically sits, which Annex 3 currently records
                        as outstanding.
                    </p>
                </div>

            </div>
        </div>
    </section>
</x-layouts.app>
