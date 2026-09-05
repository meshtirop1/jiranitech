<?php

namespace Database\Seeders;

use App\Models\JobOpening;
use Illuminate\Database\Seeder;

/**
 * Publication gate G-10.
 *
 * These are role definitions, not confirmed vacancies, and they ship with
 * `is_published` false. A job listing is an invitation to apply: publishing a role
 * that is not genuinely open wastes a candidate's time and damages the firm with
 * exactly the engineering community it is trying to recruit from.
 *
 * To go live: confirm the role is open and funded, set `posted_at`, then set
 * `is_published`. Until then the careers page renders its designed empty state and
 * invites speculative applications, which is honest and still converts.
 */
class JobOpeningSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->openings() as $index => $opening) {
            JobOpening::updateOrCreate(
                ['slug' => $opening['slug']],
                [...$opening, 'sort_order' => $index],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function openings(): array
    {
        return [
            [
                'slug' => 'senior-payments-engineer',
                'title' => 'Senior Payments Engineer',
                'level' => 'Senior engineer',
                'discipline' => 'fintech-payments',
                'location' => 'Eldoret, Kenya',
                'arrangement' => 'Hybrid — three days on site',
                'summary' => 'Own the money-movement layer on client engagements: mobile money and card rail integration, reconciliation that balances, and cardholder data architectures scoped for audit from the first design session.',
                'responsibilities' => [
                    'Design and build payment integrations across mobile money, card and bank rails',
                    'Own idempotency, callback handling and the unhappy paths that define payment work',
                    'Build reconciliation that proves itself rather than being inspected by hand',
                    'Keep PCI-DSS scope minimal by design and evidence it for assessment',
                ],
                'requirements' => [
                    'Five or more years building transactional systems in production',
                    'Direct experience of a payment rail integration you were on call for',
                    'Fluency in one of .NET, Laravel or an equivalent server framework',
                    'Able to explain a reconciliation break to a finance team without jargon',
                ],
            ],
            [
                'slug' => 'principal-cloud-architect',
                'title' => 'Principal Cloud Architect',
                'level' => 'Principal engineer',
                'discipline' => 'cloud-devops',
                'location' => 'Eldoret, Kenya',
                'arrangement' => 'Hybrid — three days on site',
                'summary' => 'Own the target architecture on cloud engagements and be answerable for its non-functional characteristics. Write the decision records, including the options rejected and why.',
                'responsibilities' => [
                    'Landing zone, identity and network topology design across AWS, Azure and GCP',
                    'Migration wave planning, cutover rehearsal and rollback design',
                    'Architecture decision records that survive a client security review',
                    'Technical mentorship of senior engineers on the engagement',
                ],
                'requirements' => [
                    'Eight or more years in infrastructure engineering, including cloud migration you led',
                    'Depth in at least one major cloud and working knowledge of a second',
                    'Infrastructure as Code as a default rather than an aspiration',
                    'Willing to recommend against a migration when the case is not there',
                ],
            ],
            [
                'slug' => 'ai-engineer-agentic-systems',
                'title' => 'AI Engineer — Agentic Systems',
                'level' => 'Senior engineer',
                'discipline' => 'ai-automation',
                'location' => 'Eldoret, Kenya',
                'arrangement' => 'Hybrid — three days on site',
                'summary' => 'Build agent systems that take real actions inside client estates, and build the containment around them first: tool boundaries, permission scoping, approval gates and full action audit trails.',
                'responsibilities' => [
                    'Agent topology, orchestration and tool boundary design',
                    'Evaluation harnesses and regression suites that gate deployment',
                    'Retrieval architectures that inherit the source system permission model',
                    'Human-in-the-loop approval workflows on irreversible operations',
                ],
                'requirements' => [
                    'Production experience shipping an LLM-backed system real users depended on',
                    'Strong Python, and comfort with the evaluation problem rather than only the prompt',
                    'Security instincts: you treat a retrieval index as an access control surface',
                    'Able to say when retrieval augmentation beats fine-tuning, and show the numbers',
                ],
            ],
            [
                'slug' => 'quality-engineer',
                'title' => 'Quality Engineer',
                'level' => 'Engineer',
                'discipline' => 'core-frameworks-api',
                'location' => 'Eldoret, Kenya',
                'arrangement' => 'Hybrid — three days on site',
                'summary' => 'Make our delivery pipelines trustworthy. Contract testing, regression coverage on legacy modernisation, and the automated gates that let a deployment be a non-event.',
                'responsibilities' => [
                    'Consumer-driven contract testing across service boundaries',
                    'Characterisation tests written before legacy code is changed',
                    'End-to-end and accessibility testing in CI',
                    'Keeping the pipeline failing closed on findings that matter',
                ],
                'requirements' => [
                    'Three or more years in test automation on a delivery team',
                    'Comfort with at least one of Playwright, Pact or an equivalent',
                    'A view on what should not be automated as well as what should',
                ],
            ],
        ];
    }
}
