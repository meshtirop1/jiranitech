<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->industries() as $index => $industry) {
            Industry::updateOrCreate(
                ['slug' => $industry['slug']],
                [...$industry, 'sort_order' => $index],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function industries(): array
    {
        return [
            [
                'slug' => 'banking-financial-services',
                'title' => 'Banking & Financial Services',
                'constraint_statement' => 'Core system integration, payment rail engineering, regulatory reporting and PCI-scoped architecture — built for institutions that answer to a regulator.',
                'pressures' => [
                    'Core banking systems that cannot be taken offline for a migration window',
                    'Payment rails that must reconcile to the last shilling, every day',
                    'Cardholder data scope that expands quietly and is expensive to reduce later',
                    'Regulatory reporting obligations that change faster than the systems producing them',
                ],
                'regulatory_notes' => 'Engagements are structured around PCI-DSS v4.0 scope control, Central Bank of Kenya guidance on outsourcing and technology risk, and the Kenya Data Protection Act 2019. Where a client is subject to obligations we do not routinely work under, we say so before contract rather than after.',
            ],
            [
                'slug' => 'public-sector',
                'title' => 'Public Sector & Government',
                'constraint_statement' => 'Citizen-facing service platforms, data residency and interoperability, delivered against procurement, accessibility and audit requirements.',
                'pressures' => [
                    'Procurement processes that require evidence before they permit a conversation',
                    'Data residency obligations that constrain which cloud regions are available',
                    'Accessibility conformance as a legal requirement rather than a quality target',
                    'Interoperability across agencies whose systems were never designed to meet',
                ],
                'regulatory_notes' => 'Delivery is structured for prequalification and audit: documented change control, WCAG 2.2 AA conformance, in-country data residency options, and a full handover package so the agency is never dependent on a single supplier.',
            ],
            [
                'slug' => 'commerce-and-marketplaces',
                'title' => 'Commerce & Marketplaces',
                'constraint_statement' => 'Multi-vendor platforms, real-time auction and trading engines, settlement and fraud control — the domain our own group operates in.',
                'pressures' => [
                    'Concurrency at auction close, where correctness is the entire problem',
                    'Settlement across multiple payment rails with different failure semantics',
                    'Fraud and abuse that adapt faster than static rules can respond',
                    'Catalogue and search performance under seasonal demand spikes',
                ],
                'regulatory_notes' => 'Consumer protection obligations, transaction record retention, and merchant settlement terms. This is the sector Jiranisoko Marketplace operates in, so our reference architecture here is drawn from a system the group is accountable for.',
            ],
            [
                'slug' => 'high-growth-ventures',
                'title' => 'High-Growth Ventures',
                'constraint_statement' => 'Product engineering at investor pace, with the architecture discipline that keeps a Series-A system from becoming a Series-C liability.',
                'pressures' => [
                    'A roadmap set by investor milestones rather than by engineering readiness',
                    'Hiring that cannot keep pace with the delivery commitments already made',
                    'Technical debt taken deliberately that nobody has scheduled to repay',
                    'Due diligence that will examine the codebase before the next round closes',
                ],
                'regulatory_notes' => 'We document the architectural trade-offs taken under time pressure so that technical due diligence finds a considered position rather than an accident.',
            ],
        ];
    }
}
