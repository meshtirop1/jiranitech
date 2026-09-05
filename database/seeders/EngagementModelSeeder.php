<?php

namespace Database\Seeders;

use App\Enums\RfpTrack;
use App\Models\EngagementModel;
use Illuminate\Database\Seeder;

class EngagementModelSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->models() as $index => $model) {
            EngagementModel::updateOrCreate(
                ['slug' => $model['slug']],
                [...$model, 'sort_order' => $index],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function models(): array
    {
        return [
            [
                'slug' => 'turnkey-solution-delivery',
                'reference' => 'Model A',
                'title' => 'Turnkey Solution Delivery',
                'summary' => 'Fixed-scope, end-to-end accountability. We take the requirement through architecture, engineering, security review, deployment and SLA-governed operation, and we are answerable for the outcome.',
                'commercial_basis' => 'Fixed price, or capped time-and-materials against an agreed scope baseline. Change is handled through a written variation rather than absorbed silently.',
                'scope_boundary' => 'We own delivery risk within the agreed baseline. Scope added after baseline is quoted and approved before work begins.',
                'governance_cadence' => 'Weekly delivery report, fortnightly steering review, and a written architecture decision record for every material design choice.',
                'suited_to' => 'Defined programmes with a hard delivery date and an internal team without the capacity to execute them.',
                'rfp_track' => RfpTrack::NewProductBuild->value,
            ],
            [
                'slug' => 'dedicated-engineering-teams',
                'reference' => 'Model B',
                'title' => 'Dedicated Engineering Teams',
                'summary' => 'Senior engineering pods integrated into your workflow, your board, your standup and your definition of done. You direct the work; we own recruitment, retention, bench cover and technical quality.',
                'commercial_basis' => 'Monthly per-seat retainer with a ninety-day minimum term and thirty days notice to scale down.',
                'scope_boundary' => 'You set priorities and accept the work. We guarantee the seniority, continuity and technical quality of the named engineers.',
                'governance_cadence' => 'Embedded in your existing ceremonies, with a monthly capability review between your engineering lead and ours.',
                'suited_to' => 'Product organisations scaling faster than they can hire, working to an evolving roadmap.',
                'rfp_track' => RfpTrack::TeamAugmentation->value,
            ],
            [
                'slug' => 'strategic-advisory',
                'reference' => 'Model C',
                'title' => 'Enterprise Strategic Advisory',
                'summary' => 'Independent architectural, security and transformation counsel — including the assessment of work we did not build and would not be asked to rebuild.',
                'commercial_basis' => 'Fixed-fee assessment against a defined question, or retained advisory days drawn down monthly.',
                'scope_boundary' => 'We advise and step back. Where an assessment recommends work we could deliver, we say so explicitly and you remain free to place it elsewhere.',
                'governance_cadence' => 'Written findings, a target architecture, a sequenced roadmap and costed options, presented to the board or executive sponsor.',
                'suited_to' => 'Boards and chief information officers who need a defensible basis for a major technology decision.',
                'rfp_track' => RfpTrack::StrategicAdvisory->value,
            ],
        ];
    }
}
