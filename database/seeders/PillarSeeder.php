<?php

namespace Database\Seeders;

use App\Models\Pillar;
use Illuminate\Database\Seeder;

class PillarSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->pillars() as $index => $pillar) {
            $services = $pillar['services'];
            unset($pillar['services']);

            $record = Pillar::updateOrCreate(
                ['slug' => $pillar['slug']],
                [...$pillar, 'sort_order' => $index],
            );

            foreach ($services as $position => $service) {
                $record->services()->updateOrCreate(
                    ['slug' => $service['slug']],
                    [...$service, 'sort_order' => $position],
                );
            }
        }
    }

    /**
     * The capability taxonomy from JTS-WEB-IA-001 section 2.3.
     *
     * @return array<int, array<string, mixed>>
     */
    private function pillars(): array
    {
        return [
            $this->artificialIntelligence(),
            $this->cloudAndDevOps(),
            $this->enterpriseSoftware(),
            $this->financialTechnology(),
            $this->coreFrameworks(),
            $this->decentralisedWeb(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function artificialIntelligence(): array
    {
        return [
            'number' => 1,
            'slug' => 'ai-automation',
            'title' => 'Artificial Intelligence & Automation',
            'nav_title' => 'AI & Automation',
            'descriptor' => 'From AI ambition to production systems that carry measurable operational load.',
            'thesis' => 'We move organisations from AI ambition to production systems that carry measurable operational load — with the evaluation, guardrails and human oversight that make them defensible to a risk committee.',
            'services' => [
                [
                    'slug' => 'ai-readiness-assessment',
                    'title' => 'AI Readiness Assessment & Enterprise AI Strategy',
                    'executive_summary' => 'A structured assessment of where artificial intelligence will repay investment in your organisation, and where it will not. We examine data estate, process maturity, regulatory exposure and internal capability, and return a sequenced roadmap with costed options rather than a list of possibilities.',
                    'outcomes' => ['A ranked portfolio of AI opportunities with estimated value and delivery cost', 'An honest statement of which initiatives your data cannot yet support', 'A governance model covering model risk, human oversight and audit', 'A twelve-month sequenced roadmap your board can approve'],
                    'capabilities' => ['Data estate and pipeline maturity review', 'Use-case discovery and value modelling', 'Model risk and AI governance framework design', 'Build-versus-buy analysis for foundation models', 'Capability and operating model assessment'],
                    'stack' => ['Python', 'dbt', 'Snowflake', 'BigQuery', 'Azure AI Foundry', 'Amazon Bedrock'],
                    'architecture_note' => 'Assessment engagements are deliberately technology-neutral. We do not recommend a platform until the use-case portfolio is agreed.',
                    'sla_tier' => 'gold',
                    'compliance_tags' => ['ISO/IEC 42001', 'Kenya DPA 2019', 'GDPR Art. 22'],
                    'meta_description' => 'Structured AI readiness assessment and enterprise AI strategy for institutions — data maturity, use-case portfolio, model governance and a costed roadmap.',
                ],
                [
                    'slug' => 'agentic-ai-architectures',
                    'title' => 'Agentic AI & Autonomous Agent Architectures',
                    'executive_summary' => 'We design and build agent systems that take real actions inside your estate — and we build the containment around them first. Tool boundaries, permission scoping, human approval gates and full action audit trails are part of the architecture, not a later hardening pass.',
                    'outcomes' => ['Autonomous handling of defined multi-step processes end to end', 'A complete audit trail of every action an agent took and why', 'Explicit human approval gates on irreversible operations', 'Measured task completion rates against a held-out evaluation set'],
                    'capabilities' => ['Agent topology and orchestration design', 'Tool and function-calling boundary definition', 'Permission scoping and least-privilege action design', 'Evaluation harness and regression suite construction', 'Human-in-the-loop approval workflow engineering'],
                    'stack' => ['Foundation model APIs', 'Model Context Protocol', 'LangGraph', 'Temporal', 'Redis', 'PostgreSQL'],
                    'architecture_note' => 'Every agent action passes through a policy layer that is versioned, tested and auditable independently of the model itself.',
                    'sla_tier' => 'platinum',
                    'compliance_tags' => ['ISO/IEC 42001', 'SOC 2', 'Audit trail retention'],
                    'meta_description' => 'Enterprise agentic AI architecture — orchestration, tool boundaries, permission scoping, approval gates and auditable autonomous agent systems.',
                ],
                [
                    'slug' => 'llm-fine-tuning-deployment',
                    'title' => 'LLM Fine-Tuning & Private Model Deployment',
                    'executive_summary' => 'Where a general model is insufficient or your data cannot leave your boundary, we fine-tune and deploy models inside infrastructure you control. We are equally willing to tell you that retrieval augmentation would meet the requirement at a fraction of the cost.',
                    'outcomes' => ['Model performance on your domain measured against a documented baseline', 'Inference running inside your own network or tenancy boundary', 'Predictable per-token cost under your own capacity planning', 'A retraining pipeline your team can operate without us'],
                    'capabilities' => ['Training corpus construction and cleaning', 'Supervised fine-tuning and preference optimisation', 'Retrieval-augmented generation architecture', 'Private and VPC-isolated inference deployment', 'Evaluation, benchmarking and drift monitoring'],
                    'stack' => ['PyTorch', 'Hugging Face', 'vLLM', 'Amazon SageMaker', 'Azure ML', 'Kubernetes'],
                    'architecture_note' => 'We benchmark retrieval augmentation against fine-tuning before recommending either. Fine-tuning is frequently the more expensive answer to a retrieval problem.',
                    'sla_tier' => 'platinum',
                    'compliance_tags' => ['Data residency', 'Kenya DPA 2019', 'GDPR Art. 28'],
                    'meta_description' => 'LLM fine-tuning and private model deployment — domain adaptation, VPC-isolated inference, evaluation harnesses and drift monitoring.',
                ],
                [
                    'slug' => 'enterprise-copilots',
                    'title' => 'Enterprise Copilots & Conversational Assistants',
                    'executive_summary' => 'Assistants grounded in your own systems of record, scoped to what each user is permitted to see. We treat permission inheritance as the hard problem it is: a copilot that answers from documents a user could not otherwise open is a data breach with a friendly interface.',
                    'outcomes' => ['Measurable reduction in time spent retrieving internal information', 'Answers grounded in cited source documents rather than model recall', 'Permission parity with the underlying systems of record', 'Deflection rates reported against a defined query taxonomy'],
                    'capabilities' => ['Retrieval architecture over enterprise content', 'Row-level and document-level permission inheritance', 'Citation and source attribution engineering', 'Escalation and handover to human agents', 'Conversation analytics and answer quality review'],
                    'stack' => ['Foundation model APIs', 'Azure AI Search', 'pgvector', 'Elasticsearch', '.NET', 'Angular'],
                    'architecture_note' => 'Retrieval executes under the querying user identity. The assistant cannot surface a document the user could not open directly.',
                    'sla_tier' => 'gold',
                    'compliance_tags' => ['Access control parity', 'Kenya DPA 2019', 'GDPR Art. 28'],
                    'meta_description' => 'Enterprise copilots and conversational AI grounded in your systems of record with permission parity, citations and human escalation.',
                ],
                [
                    'slug' => 'workflow-automation',
                    'title' => 'AI-Driven Workflow Automation & Process Optimisation',
                    'executive_summary' => 'We instrument the process before we automate it. Automating a workflow nobody has measured usually encodes the inefficiency permanently, so our engagements begin with observation and end with a system whose throughput is reported against the baseline we established.',
                    'outcomes' => ['Documented cycle-time reduction against a measured pre-automation baseline', 'Exception paths handled explicitly rather than failing silently', 'Straight-through processing rates reported per workflow', 'Staff redeployed from transcription to judgement'],
                    'capabilities' => ['Process mining and baseline instrumentation', 'Document ingestion, extraction and classification', 'Decision automation with confidence thresholds', 'Exception routing and manual review queues', 'Throughput and accuracy reporting'],
                    'stack' => ['Python', 'Temporal', 'Camunda', 'Azure Document Intelligence', 'PostgreSQL', 'Power BI'],
                    'architecture_note' => 'Every automated decision records its confidence score. Below threshold, work routes to a human queue rather than proceeding.',
                    'sla_tier' => 'gold',
                    'compliance_tags' => ['Audit trail retention', 'SOC 2', 'Kenya DPA 2019'],
                    'meta_description' => 'AI-driven workflow automation and process optimisation with measured baselines, confidence thresholds and explicit exception handling.',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cloudAndDevOps(): array
    {
        return [
            'number' => 2,
            'slug' => 'cloud-devops',
            'title' => 'Cloud Infrastructure & DevOps',
            'nav_title' => 'Cloud & DevOps',
            'descriptor' => 'Cloud estates that are auditable, reproducible and cost-governed.',
            'thesis' => 'We design and operate cloud estates that are auditable, reproducible and cost-governed — where every environment is defined in code, every change is reviewable, and every deployment is a non-event.',
            'services' => [
                [
                    'slug' => 'cloud-strategy-architecture',
                    'title' => 'Cloud Strategy, Architecture & Consulting',
                    'executive_summary' => 'Target-state cloud architecture across AWS, Microsoft Azure and Google Cloud Platform, with the landing zone, network topology, identity model and cost controls specified before the first workload moves. We design for the regulatory position you are actually in, including data residency obligations.',
                    'outcomes' => ['A target architecture your security function has reviewed and accepted', 'Landing zone and account topology aligned to your control requirements', 'Cost visibility by business unit from the first month of operation', 'A migration sequence ordered by risk rather than by convenience'],
                    'capabilities' => ['Landing zone and account or subscription topology design', 'Network, connectivity and hybrid architecture', 'Identity, access and privileged access modelling', 'FinOps, tagging strategy and cost governance', 'Multi-cloud and exit-risk assessment'],
                    'stack' => ['AWS', 'Microsoft Azure', 'Google Cloud Platform', 'Terraform', 'Cloudflare'],
                    'architecture_note' => 'Landing zone design precedes workload migration in every engagement. Retrofitting account topology after workloads land is materially more expensive.',
                    'sla_tier' => 'platinum',
                    'compliance_tags' => ['ISO/IEC 27001', 'Data residency', 'CIS Benchmarks'],
                    'meta_description' => 'Cloud strategy and architecture consulting for AWS, Azure and Google Cloud — landing zones, identity, network topology and cost governance.',
                ],
                [
                    'slug' => 'cloud-data-migration',
                    'title' => 'Cloud & Data Migration Services',
                    'executive_summary' => 'Migration with a rehearsed cutover and a tested rollback. We move workloads and data estates on a sequence ordered by risk, and we do not schedule a production cutover that has not been executed end to end in a staging environment first.',
                    'outcomes' => ['Cutover executed inside the agreed maintenance window', 'Data integrity verified by reconciliation rather than by inspection', 'A rollback path proven before cutover, not designed during it', 'Legacy estate decommissioned on a defined schedule'],
                    'capabilities' => ['Application dependency discovery and mapping', 'Migration wave planning and sequencing', 'Database and data warehouse migration', 'Cutover rehearsal, runbook and rollback design', 'Post-migration reconciliation and optimisation'],
                    'stack' => ['AWS DMS', 'Azure Migrate', 'Terraform', 'PostgreSQL', 'MySQL', 'Microsoft SQL Server'],
                    'architecture_note' => 'Reconciliation is row-count and checksum based, executed automatically at cutover and reported before traffic is switched.',
                    'sla_tier' => 'platinum',
                    'compliance_tags' => ['Data residency', 'Kenya DPA 2019', 'Change management'],
                    'meta_description' => 'Cloud and data migration services with rehearsed cutovers, tested rollback paths and automated post-migration reconciliation.',
                ],
                [
                    'slug' => 'devops-cicd-iac',
                    'title' => 'Enterprise DevOps, CI/CD Automation & Infrastructure as Code',
                    'executive_summary' => 'Delivery pipelines that make deployment routine and infrastructure that can be rebuilt from source. We instrument the four delivery metrics from the outset so that improvement is demonstrated rather than asserted.',
                    'outcomes' => ['Deployment frequency raised and lead time reduced against a measured baseline', 'Every environment reproducible from version-controlled source', 'Security and dependency scanning enforced in the pipeline rather than advisory', 'Change failure rate and recovery time reported to management'],
                    'capabilities' => ['CI/CD pipeline design and implementation', 'Infrastructure as Code with policy enforcement', 'Container platform and orchestration engineering', 'Secrets management and supply-chain scanning', 'Observability, alerting and on-call design'],
                    'stack' => ['GitHub Actions', 'Azure DevOps', 'Terraform', 'Docker', 'Kubernetes', 'Grafana', 'HashiCorp Vault'],
                    'architecture_note' => 'Pipelines fail closed on critical vulnerability findings. A scanning stage that only warns is a reporting tool, not a control.',
                    'sla_tier' => 'gold',
                    'compliance_tags' => ['ISO/IEC 27001', 'SOC 2', 'Change management'],
                    'meta_description' => 'Enterprise DevOps, CI/CD pipeline automation and Infrastructure as Code with enforced security scanning and delivery metrics.',
                ],
                [
                    'slug' => 'serverless-microservices',
                    'title' => 'Serverless Architecture & Microservices Engineering',
                    'executive_summary' => 'Service decomposition driven by team ownership and failure isolation rather than by fashion. We are candid that a well-structured monolith is the correct answer for a significant share of the systems we are asked to decompose.',
                    'outcomes' => ['Services aligned to team boundaries and independently deployable', 'Failure in one service contained rather than propagated', 'Infrastructure cost tracking demand rather than peak provisioning', 'Distributed traces that make a cross-service incident diagnosable'],
                    'capabilities' => ['Domain decomposition and service boundary design', 'Event-driven and asynchronous messaging architecture', 'Serverless compute and managed service adoption', 'API gateway, service mesh and traffic management', 'Distributed tracing and correlation'],
                    'stack' => ['AWS Lambda', 'Azure Functions', 'Kubernetes', 'Apache Kafka', 'RabbitMQ', 'OpenTelemetry'],
                    'architecture_note' => 'We require a demonstrated scaling, ownership or failure-isolation driver before recommending decomposition of a working monolith.',
                    'sla_tier' => 'gold',
                    'compliance_tags' => ['ISO/IEC 27001', 'Availability design'],
                    'meta_description' => 'Serverless architecture and microservices engineering — domain decomposition, event-driven messaging, service mesh and distributed tracing.',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function enterpriseSoftware(): array
    {
        return [
            'number' => 3,
            'slug' => 'enterprise-software',
            'title' => 'Enterprise Software & SaaS Solutions',
            'nav_title' => 'Enterprise Software',
            'descriptor' => 'Multi-tenant products and platforms built to survive their own success.',
            'thesis' => 'We engineer multi-tenant products and internal platforms built to survive their own success — tenancy isolation, data partitioning and reporting designed in from the first sprint rather than retrofitted under load.',
            'services' => [
                [
                    'slug' => 'saas-product-engineering',
                    'title' => 'Custom SaaS Product Engineering',
                    'executive_summary' => 'Multi-tenant platforms where the tenancy model is an architectural decision taken deliberately at the outset. Isolation strategy, per-tenant data residency, noisy-neighbour control and tenant-scoped observability are specified before the first feature is built, because none of them can be added cheaply afterwards.',
                    'outcomes' => ['A tenancy model that holds at your projected tenant count, not just your current one', 'Per-tenant usage metering suitable for billing', 'Tenant onboarding measured in minutes rather than engineering days', 'Isolation you can evidence to a security reviewer'],
                    'capabilities' => ['Tenancy and data isolation strategy', 'Subscription, entitlement and feature-flag architecture', 'Per-tenant metering and usage aggregation', 'Administrative and tenant-support tooling', 'Load, soak and multi-tenant performance testing'],
                    'stack' => ['Laravel', '.NET', 'Angular', 'PostgreSQL', 'MySQL', 'Redis', 'Kubernetes'],
                    'architecture_note' => 'Tenant identity is resolved in middleware and enforced at the query layer, so a missing scope is a failing test rather than a cross-tenant data leak.',
                    'sla_tier' => 'platinum',
                    'compliance_tags' => ['SOC 2', 'Tenant isolation', 'GDPR Art. 28'],
                    'meta_description' => 'Custom multi-tenant SaaS product engineering — tenancy isolation, entitlement architecture, usage metering and tenant-scoped observability.',
                ],
                [
                    'slug' => 'mobile-application-development',
                    'title' => 'Enterprise Mobile Application Development',
                    'executive_summary' => 'Native and cross-platform applications engineered for the network conditions your users actually have. For East African deployments that means offline-first synchronisation, conflict resolution and payload discipline as default requirements rather than enhancements.',
                    'outcomes' => ['Applications that remain usable through intermittent connectivity', 'Synchronisation conflicts resolved by defined rules rather than last-write-wins', 'Store release pipelines your team can operate independently', 'Crash-free session rates reported against a target'],
                    'capabilities' => ['Native iOS and Android engineering', 'Cross-platform delivery with Flutter and React Native', 'Offline-first data synchronisation and conflict resolution', 'Mobile device management and enterprise distribution', 'Release automation and staged rollout'],
                    'stack' => ['Swift', 'Kotlin', 'Flutter', 'React Native', 'Firebase', 'SQLite'],
                    'architecture_note' => 'Synchronisation uses an explicit conflict policy per entity. Last-write-wins is a decision to be taken deliberately, not a default to inherit.',
                    'sla_tier' => 'gold',
                    'compliance_tags' => ['Mobile data protection', 'Kenya DPA 2019', 'Store compliance'],
                    'meta_description' => 'Enterprise mobile application development for iOS, Android and cross-platform with offline-first synchronisation and staged release automation.',
                ],
                [
                    'slug' => 'auction-trading-engines',
                    'title' => 'Real-Time Auction & Trading Engine Platforms',
                    'executive_summary' => 'Bidding and matching systems where correctness under contention is the entire engineering problem. Our group operates a marketplace carrying live payment rails and real concurrency, so our treatment of race conditions, idempotency and settlement finality comes from systems we are accountable for rather than from reference architecture.',
                    'outcomes' => ['Bid ordering that is deterministic and defensible in a dispute', 'Auction close handled correctly under concurrent final-second bidding', 'Latency measured and reported at the ninety-ninth percentile', 'A settlement record that reconciles to the payment ledger'],
                    'capabilities' => ['Matching and bid engine design', 'Concurrency, locking and idempotency architecture', 'Real-time distribution over WebSocket and server-sent events', 'Anti-sniping, reserve and proxy bidding logic', 'Settlement, escrow and payout integration'],
                    'stack' => ['.NET', 'Laravel', 'Redis', 'PostgreSQL', 'Apache Kafka', 'WebSocket'],
                    'architecture_note' => 'Bids are appended to an immutable ledger and ordered by server receipt sequence. Auction state is derived from that ledger, never edited in place.',
                    'sla_tier' => 'platinum',
                    'compliance_tags' => ['Audit trail retention', 'Settlement reconciliation', 'SOC 2'],
                    'meta_description' => 'Real-time auction and trading engine platforms — deterministic bid ordering, concurrency control, anti-sniping logic and settlement integration.',
                ],
                [
                    'slug' => 'business-intelligence-analytics',
                    'title' => 'Business Intelligence, Analytics & Executive Dashboards',
                    'executive_summary' => 'Reporting built on a modelled semantic layer so that a figure means the same thing in every dashboard that shows it. Most reporting programmes fail on definitional inconsistency rather than on visualisation, so we govern the metric definitions first.',
                    'outcomes' => ['One agreed definition per metric, enforced in the semantic layer', 'Executive reporting available without an analyst in the loop', 'Data freshness stated explicitly on every dashboard', 'Self-service analysis within governed boundaries'],
                    'capabilities' => ['Dimensional modelling and semantic layer design', 'ELT pipeline engineering and orchestration', 'Executive and operational dashboard delivery', 'Data quality testing and freshness monitoring', 'Row-level security and access governance'],
                    'stack' => ['dbt', 'Snowflake', 'BigQuery', 'Power BI', 'Apache Airflow', 'PostgreSQL'],
                    'architecture_note' => 'Every dashboard surfaces its own data freshness timestamp. A stale figure presented without qualification is worse than no figure.',
                    'sla_tier' => 'gold',
                    'compliance_tags' => ['Row-level security', 'Kenya DPA 2019', 'Data lineage'],
                    'meta_description' => 'Business intelligence, advanced analytics and executive dashboards built on a governed semantic layer with tested data quality and freshness.',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function financialTechnology(): array
    {
        return [
            'number' => 4,
            'slug' => 'fintech-payments',
            'title' => 'Financial Technology & Payment Ecosystems',
            'nav_title' => 'FinTech & Payments',
            'descriptor' => 'The money-movement layer: rails, reconciliation and audit-ready scope.',
            'thesis' => 'We build the money-movement layer: integration with mobile money and card rails, reconciliation that balances, and cardholder-data architectures scoped for audit from the first day rather than the first assessment.',
            'services' => [
                [
                    'slug' => 'payment-gateway-engineering',
                    'title' => 'Payment Gateway Integration & Custom Gateway Development',
                    'executive_summary' => 'Integration across mobile money, card and bank rails behind a single internal interface, with the failure modes handled explicitly. Payment integration is defined by its unhappy paths — timeouts, duplicate callbacks, partial reversals — and that is where our engineering effort concentrates.',
                    'outcomes' => ['Multiple payment rails behind one internal payment interface', 'Idempotent handling of duplicated and out-of-order callbacks', 'Failed and pending transactions resolved by automated reconciliation', 'Rail-level success rates reported and alertable'],
                    'capabilities' => ['Mobile money integration including M-Pesa Daraja', 'Card acquiring and 3-D Secure integration', 'Bank transfer and PesaLink integration', 'Payment orchestration and rail failover', 'Webhook, callback and idempotency engineering'],
                    'stack' => ['Laravel', '.NET', 'PostgreSQL', 'Redis', 'RabbitMQ', 'M-Pesa Daraja API'],
                    'architecture_note' => 'Every inbound callback is verified, deduplicated by provider reference and processed exactly once. Payment state transitions are append-only.',
                    'sla_tier' => 'platinum',
                    'compliance_tags' => ['PCI-DSS v4.0', 'CBK guidance', 'Transaction audit trail'],
                    'meta_description' => 'Payment gateway integration and custom gateway engineering across mobile money, card and bank rails with idempotent callback handling.',
                ],
                [
                    'slug' => 'billing-subscription-engines',
                    'title' => 'Institutional Billing, Invoicing & Subscription Engines',
                    'executive_summary' => 'Billing systems that compute the correct amount under proration, mid-cycle change, tax and multi-currency, and that produce an invoice your finance function can reconcile. Billing defects are discovered by customers and are expensive in both revenue and trust.',
                    'outcomes' => ['Invoices that reconcile to the general ledger without manual adjustment', 'Proration and mid-cycle changes computed correctly and testably', 'Dunning and retry logic that recovers involuntary churn', 'Revenue recognition data your finance team can rely on'],
                    'capabilities' => ['Subscription lifecycle and plan modelling', 'Usage metering, rating and proration', 'Invoice generation, tax handling and multi-currency', 'Dunning, retry and collections workflow', 'Ledger integration and revenue reporting'],
                    'stack' => ['Laravel', '.NET', 'PostgreSQL', 'MySQL', 'Redis', 'KRA eTIMS'],
                    'architecture_note' => 'Billing runs are idempotent and replayable against a fixed period. A rerun produces the same invoice rather than a second one.',
                    'sla_tier' => 'platinum',
                    'compliance_tags' => ['KRA eTIMS', 'Financial audit trail', 'SOC 2'],
                    'meta_description' => 'Institutional billing, invoicing and subscription engine engineering with proration, multi-currency, dunning and ledger reconciliation.',
                ],
                [
                    'slug' => 'transaction-processing-pci-dss',
                    'title' => 'Core Transaction Processing & PCI-DSS Compliant Architecture',
                    'executive_summary' => 'High-integrity transaction processing with cardholder data scope deliberately minimised. The most valuable architectural decision in a payments programme is how much of your estate falls inside the assessment boundary, and that decision is taken at design time.',
                    'outcomes' => ['Cardholder data scope reduced to an evidenced minimum', 'Double-entry ledger that balances continuously', 'Transaction throughput sustained under peak load testing', 'Assessment evidence produced from the system rather than assembled by hand'],
                    'capabilities' => ['PCI-DSS scope reduction and segmentation design', 'Tokenisation and vaulting architecture', 'Double-entry ledger engineering', 'Fraud signal capture and rule engine integration', 'Reconciliation, settlement and dispute handling'],
                    'stack' => ['.NET', 'PostgreSQL', 'Apache Kafka', 'HashiCorp Vault', 'Kubernetes'],
                    'architecture_note' => 'Card data is tokenised at the network edge. Application services hold tokens only, which keeps the majority of the estate outside assessment scope.',
                    'sla_tier' => 'platinum',
                    'compliance_tags' => ['PCI-DSS v4.0', 'Segmentation testing', 'CBK guidance'],
                    'meta_description' => 'Core transaction processing and PCI-DSS compliant architecture — scope reduction, tokenisation, double-entry ledger and settlement reconciliation.',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function coreFrameworks(): array
    {
        return [
            'number' => 5,
            'slug' => 'core-frameworks-api',
            'title' => 'Core Frameworks & API Engineering',
            'nav_title' => 'Frameworks & API',
            'descriptor' => 'Modernisation of systems you depend on, exposed through hardened APIs.',
            'thesis' => 'We modernise the systems institutions already depend on, and expose them through interfaces that are versioned, contract-tested and defensible under security review.',
            'services' => [
                [
                    'slug' => 'dotnet-engineering-modernization',
                    'title' => 'Enterprise Microsoft .NET Development & Modernisation',
                    'executive_summary' => 'Incremental modernisation of .NET estates without a rewrite. We move systems forward behind a routing facade so that value is delivered continuously and the programme can be stopped at any point without leaving the estate in a worse state than it started.',
                    'outcomes' => ['A supported framework version with a defined upgrade path', 'Modernisation delivered incrementally rather than as one cutover event', 'Regression risk contained by characterisation tests written before change', 'Operating cost reduced through consolidation and right-sizing'],
                    'capabilities' => ['.NET Framework to modern .NET migration', 'Strangler-pattern decomposition of monoliths', 'Characterisation and regression test construction', 'ASP.NET Core and Entity Framework Core engineering', 'Windows to Linux and container migration'],
                    'stack' => ['.NET', 'ASP.NET Core', 'Entity Framework Core', 'Microsoft SQL Server', 'Azure', 'Docker'],
                    'architecture_note' => 'A routing facade fronts old and new implementations so traffic moves per endpoint and reverts instantly if a regression appears.',
                    'sla_tier' => 'gold',
                    'compliance_tags' => ['ISO/IEC 27001', 'Change management', 'Supported versions'],
                    'meta_description' => 'Enterprise .NET development and legacy modernisation — incremental strangler migration, characterisation testing and container adoption.',
                ],
                [
                    'slug' => 'angular-web-applications',
                    'title' => 'High-Performance Angular Web Applications',
                    'executive_summary' => 'Angular applications engineered for the devices and bandwidth of your actual users. We hold a performance budget from the first commit, because front-end performance is defended continuously or lost permanently.',
                    'outcomes' => ['Core Web Vitals within target on mid-range devices and networks', 'A bundle budget enforced in the build pipeline', 'WCAG 2.2 AA conformance verified rather than assumed', 'A component library your own team can extend'],
                    'capabilities' => ['Angular application architecture and state management', 'Performance budgeting, lazy loading and bundle analysis', 'Accessibility engineering to WCAG 2.2 AA', 'Design system and component library construction', 'Server-side rendering and hydration'],
                    'stack' => ['Angular', 'TypeScript', 'RxJS', 'NgRx', 'Vite', 'Playwright'],
                    'architecture_note' => 'The build fails when the bundle budget is exceeded. A performance target that does not fail a build is a preference, not a budget.',
                    'sla_tier' => 'gold',
                    'compliance_tags' => ['WCAG 2.2 AA', 'Performance budget', 'Browser support matrix'],
                    'meta_description' => 'High-performance Angular web application engineering with enforced bundle budgets, WCAG 2.2 AA accessibility and design system delivery.',
                ],
                [
                    'slug' => 'api-design-integration-security',
                    'title' => 'API Design, Integration, Testing & Security Audit',
                    'executive_summary' => 'APIs specified before they are built, contract-tested against their consumers, and audited against the OWASP API risks. We also review APIs we did not build, and we report what we find without softening it.',
                    'outcomes' => ['A published contract consumers can build against with confidence', 'Breaking changes caught in the pipeline rather than by a consumer', 'Authorisation defects identified before exposure, not after', 'Documentation generated from the specification and therefore current'],
                    'capabilities' => ['OpenAPI-first design and governance', 'Consumer-driven contract testing', 'Versioning, deprecation and lifecycle policy', 'API gateway, rate limiting and authentication design', 'OWASP API Security Top 10 audit'],
                    'stack' => ['OpenAPI', '.NET', 'Laravel', 'Kong', 'Pact', 'OAuth 2.0', 'Postman'],
                    'architecture_note' => 'Object-level authorisation is tested per endpoint and per role. Broken object-level authorisation remains the most common finding in the APIs we audit.',
                    'sla_tier' => 'platinum',
                    'compliance_tags' => ['OWASP API Top 10', 'ISO/IEC 27001', 'Penetration testing'],
                    'meta_description' => 'Scalable API design, microservices integration, contract testing and OWASP API security audits for enterprise systems.',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function decentralisedWeb(): array
    {
        return [
            'number' => 6,
            'slug' => 'web3-blockchain',
            'title' => 'Decentralised Web3 & Blockchain Engineering',
            'nav_title' => 'Web3 & Blockchain',
            'descriptor' => 'Distributed ledger applied where it outperforms a database — and only there.',
            'thesis' => 'We apply distributed ledger technology where it demonstrably outperforms a database — multi-party settlement, provenance and asset tokenisation — and we say so plainly when it does not.',
            'services' => [
                [
                    'slug' => 'enterprise-blockchain-ledgers',
                    'title' => 'Enterprise Blockchain & Private Distributed Ledgers',
                    'executive_summary' => 'Permissioned ledgers for consortia where several organisations must share a record none of them individually controls. We begin every engagement by testing whether a conventional database with strong audit would meet the requirement, and we frequently conclude that it would.',
                    'outcomes' => ['A shared record no single participant can unilaterally alter', 'Reconciliation between parties eliminated rather than automated', 'Governance and membership rules encoded and enforced', 'A documented justification for the ledger over a database'],
                    'capabilities' => ['Consortium governance and membership design', 'Permissioned network architecture and deployment', 'Chaincode and on-chain logic engineering', 'Enterprise system integration and event bridging', 'Key management and participant onboarding'],
                    'stack' => ['Hyperledger Fabric', 'R3 Corda', 'Kubernetes', 'HashiCorp Vault', '.NET'],
                    'architecture_note' => 'Every engagement opens with a suitability assessment. Where a database meets the requirement we recommend the database and say why in writing.',
                    'sla_tier' => 'gold',
                    'compliance_tags' => ['Consortium governance', 'Key management', 'ISO/IEC 27001'],
                    'meta_description' => 'Enterprise blockchain and private distributed ledger engineering for consortia, with an explicit suitability assessment before any build.',
                ],
                [
                    'slug' => 'smart-contracts-defi',
                    'title' => 'Smart Contract Architecture & DeFi Protocols',
                    'executive_summary' => 'Smart contract engineering with security treated as the primary constraint. Deployed contract defects are frequently unrecoverable, so our process is specification, invariant definition, property-based testing and independent audit before any mainnet deployment.',
                    'outcomes' => ['Contracts deployed only after independent third-party audit', 'System invariants defined explicitly and tested continuously', 'An upgrade and emergency-pause path agreed before launch', 'Gas cost modelled and optimised against realistic usage'],
                    'capabilities' => ['Protocol design and economic modelling', 'Solidity engineering and gas optimisation', 'Invariant and property-based test construction', 'Independent audit coordination and remediation', 'Upgrade, timelock and emergency-pause architecture'],
                    'stack' => ['Solidity', 'Foundry', 'Hardhat', 'Ethereum', 'OpenZeppelin', 'The Graph'],
                    'architecture_note' => 'No contract we write reaches mainnet without an independent external audit and a documented remediation of every finding.',
                    'sla_tier' => 'platinum',
                    'compliance_tags' => ['Independent audit', 'Key management', 'Upgrade governance'],
                    'meta_description' => 'Smart contract architecture and DeFi protocol engineering with invariant testing, gas optimisation and mandatory independent audit.',
                ],
                [
                    'slug' => 'tokenization-infrastructure',
                    'title' => 'Web3 Application Ecosystems & Asset Tokenisation',
                    'executive_summary' => 'Tokenisation infrastructure for real-world assets, engineered around the part that actually determines whether it works: the legal and custodial link between the token and the asset it represents. The on-chain component is rarely the hard part.',
                    'outcomes' => ['A defensible custody and legal linkage between token and asset', 'Transfer restrictions that enforce the applicable regulatory position', 'Wallet and onboarding flows usable by non-crypto-native holders', 'A provenance record that survives platform change'],
                    'capabilities' => ['Asset tokenisation and custody architecture', 'Transfer restriction and compliance-rule engineering', 'Wallet abstraction and account recovery design', 'Marketplace and secondary transfer infrastructure', 'Metadata permanence and provenance'],
                    'stack' => ['Solidity', 'ERC-3643', 'IPFS', 'Ethereum', 'Polygon', 'Node.js'],
                    'architecture_note' => 'Transfer restrictions are enforced on-chain against an identity registry, so the regulatory position holds without depending on interface behaviour.',
                    'sla_tier' => 'gold',
                    'compliance_tags' => ['Transfer restrictions', 'Custody controls', 'Kenya DPA 2019'],
                    'meta_description' => 'Web3 application ecosystems and asset tokenisation infrastructure with custody architecture, transfer restrictions and provenance permanence.',
                ],
            ],
        ];
    }
}
