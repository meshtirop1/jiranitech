<?php

namespace Database\Seeders;

use App\Enums\ComplianceStatus;
use App\Models\ComplianceClaim;
use Illuminate\Database\Seeder;

/**
 * Publication gate G-02 from JTS-WEB-IA-001 section 4.
 *
 * Every claim is seeded as Aligned or In progress. None is seeded as Certified,
 * because no certificate has been evidenced to us. Promote a claim to Certified only
 * once the certificate is held, in date, and its evidence recorded against the row.
 */
class ComplianceClaimSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->claims() as $index => $claim) {
            ComplianceClaim::updateOrCreate(
                ['standard' => $claim['standard']],
                [...$claim, 'sort_order' => $index],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function claims(): array
    {
        return [
            [
                'standard' => 'ISO/IEC 27001',
                'status' => ComplianceStatus::Aligned,
                'reviewed_on' => '2026-09-04',
                'evidence_url' => null,
            ],
            [
                'standard' => 'PCI-DSS v4.0',
                'status' => ComplianceStatus::Aligned,
                'reviewed_on' => '2026-09-04',
                'evidence_url' => null,
            ],
            [
                'standard' => 'SOC 2 Type II',
                'status' => ComplianceStatus::InProgress,
                'reviewed_on' => '2026-09-04',
                'evidence_url' => null,
            ],
            [
                'standard' => 'Kenya Data Protection Act 2019',
                'status' => ComplianceStatus::Aligned,
                'reviewed_on' => '2026-09-04',
                'evidence_url' => null,
            ],
            [
                'standard' => 'GDPR Article 28',
                'status' => ComplianceStatus::Aligned,
                'reviewed_on' => '2026-09-04',
                'evidence_url' => null,
            ],
            [
                'standard' => 'WCAG 2.2 AA',
                'status' => ComplianceStatus::Aligned,
                'reviewed_on' => '2026-09-04',
                'evidence_url' => null,
            ],
        ];
    }
}
