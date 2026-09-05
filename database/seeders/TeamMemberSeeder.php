<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

/**
 * Publication gate G-09.
 *
 * These are posts, not people. Each row states an accountability that exists in the
 * delivery organisation — which is a structural fact about how the firm is run — and
 * leaves `name` and `photo_path` null. Seeding invented executives onto a page whose
 * whole purpose is establishing who is answerable would be a fabricated record, and
 * procurement reviewers verify these names.
 *
 * To go live: set `name` and `photo_path` on each row for the real appointee, with
 * their consent to external publication. The card layout is unchanged either way.
 */
class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->posts() as $index => $post) {
            TeamMember::updateOrCreate(
                ['role_title' => $post['role_title']],
                [...$post, 'sort_order' => $index],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function posts(): array
    {
        return [
            [
                'name' => null,
                'role_title' => 'Managing Director',
                'discipline' => null,
                'accountability' => 'Answerable to the board of Jiranisoko Market Ltd for the division as a whole: commercial performance, client relationships at executive level, and the standards the firm holds itself to.',
                'remit' => [
                    'Executive sponsor on Tier-1 and public-sector engagements',
                    'Final escalation point for any client dispute',
                    'Accountable for the published compliance and service commitments',
                ],
            ],
            [
                'name' => null,
                'role_title' => 'Chief Technology Officer',
                'discipline' => null,
                'accountability' => 'Owns the technical direction of the division and the architecture standard applied across all six disciplines. Signs off the architecture decision record on every engagement above an agreed threshold.',
                'remit' => [
                    'Architecture review and technical risk sign-off',
                    'Engineering ladder, hiring bar and technical development',
                    'Technology selection policy and its exceptions',
                ],
            ],
            [
                'name' => null,
                'role_title' => 'Director of Delivery',
                'discipline' => null,
                'accountability' => 'Accountable for every engagement meeting its baseline, its service tier and its handover obligation. Owns the delivery reporting a client sees each week.',
                'remit' => [
                    'Engagement staffing, sequencing and capacity',
                    'Service level performance and the quarterly service review',
                    'Cutover authorisation on production migrations',
                ],
            ],
            [
                'name' => null,
                'role_title' => 'Head of Information Security',
                'discipline' => null,
                'accountability' => 'Owns the information security management system and the compliance register published on this site. Holds an independent veto on any production exposure that has not passed security review.',
                'remit' => [
                    'ISMS, control framework and audit readiness',
                    'Security review gate before production exposure',
                    'Responsible disclosure intake and incident response',
                ],
            ],
            [
                'name' => null,
                'role_title' => 'Data Protection Officer',
                'discipline' => null,
                'accountability' => 'Statutory role under the Kenya Data Protection Act 2019. Independent of delivery, reporting to the board, with authority to halt processing that cannot be justified.',
                'remit' => [
                    'Data Processing Addendum and the sub-processor register',
                    'Data subject rights requests and breach notification',
                    'Data protection impact assessments',
                ],
            ],
            [
                'name' => null,
                'role_title' => 'Practice Lead — Artificial Intelligence & Automation',
                'discipline' => 'ai-automation',
                'accountability' => 'Technical quality across the AI and automation discipline, including the evaluation and guardrail standards that make an agent system defensible to a risk committee.',
                'remit' => [
                    'Model risk, evaluation harnesses and human oversight design',
                    'First technical responder on AI and automation enquiries',
                ],
            ],
            [
                'name' => null,
                'role_title' => 'Practice Lead — Financial Technology & Payments',
                'discipline' => 'fintech-payments',
                'accountability' => 'Technical quality across payments engineering, including PCI-DSS scope control, reconciliation correctness and the failure paths that define payment integration.',
                'remit' => [
                    'Cardholder data scope and tokenisation architecture',
                    'First technical responder on payments enquiries',
                ],
            ],
            [
                'name' => null,
                'role_title' => 'Practice Lead — Cloud Infrastructure & DevOps',
                'discipline' => 'cloud-devops',
                'accountability' => 'Technical quality across cloud architecture and delivery engineering, including landing zone design, migration cutover assurance and the pipeline controls that fail closed.',
                'remit' => [
                    'Landing zone standards and cost governance',
                    'Cutover rehearsal and rollback design',
                ],
            ],
        ];
    }
}
