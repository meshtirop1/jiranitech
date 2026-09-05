<?php

namespace Database\Seeders;

use App\Enums\InsightFormat;
use App\Models\Insight;
use App\Models\Pillar;
use Illuminate\Database\Seeder;

/**
 * Publication gate G-08 from JTS-WEB-IA-001 section 4.
 *
 * The homepage insights rail suppresses itself below three published items. These
 * three are engineering position pieces stating architectural opinions the firm holds.
 * They deliberately contain no client names, engagement claims or performance figures,
 * because none has been substantiated. Case studies must not be seeded: a case study
 * describing an engagement that did not happen is a fabricated record, and the rail is
 * designed to render correctly without any.
 */
class InsightSeeder extends Seeder
{
    public function run(): void
    {
        $pillars = Pillar::query()->pluck('id', 'slug');

        foreach ($this->insights() as $insight) {
            $pillarSlug = $insight['pillar_slug'];
            unset($insight['pillar_slug']);

            Insight::updateOrCreate(
                ['slug' => $insight['slug']],
                [...$insight, 'pillar_id' => $pillars[$pillarSlug] ?? null],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function insights(): array
    {
        return [
            [
                'slug' => 'scope-is-the-only-pci-decision-that-matters',
                'pillar_slug' => 'fintech-payments',
                'format' => InsightFormat::EngineeringNote,
                'title' => 'Scope is the only PCI decision that matters',
                'abstract_line' => 'Most of the cost of a PCI programme is decided at design time, by how much of the estate falls inside the boundary.',
                'body' => 'Organisations approaching PCI-DSS for the first time tend to treat it as a control-implementation exercise: read the requirements, apply them across the estate, present the result for assessment. This is the expensive path, and it is expensive in a way that compounds annually.'."\n\n".'The decision that determines the cost of every subsequent assessment is architectural, and it is taken before any control is implemented: how much of your system touches cardholder data at all. A tokenisation boundary placed at the network edge keeps application services, reporting databases, log aggregation and most of the engineering organisation outside the assessment scope entirely. The same estate without that boundary pulls all of it in.'."\n\n".'The practical consequence is that PCI scope should be settled during architecture, alongside the tenancy model and the network topology, and not deferred to a compliance workstream that begins after the system exists. Reducing scope retrospectively means re-architecting data flows that already have consumers, which is the most expensive form of the same work.',
                'read_minutes' => 4,
                'is_featured' => true,
                'published_at' => '2026-08-12 09:00:00',
            ],
            [
                'slug' => 'when-a-database-beats-a-ledger',
                'pillar_slug' => 'web3-blockchain',
                'format' => InsightFormat::EngineeringNote,
                'title' => 'When a database beats a ledger',
                'abstract_line' => 'A distributed ledger earns its complexity only when no single participant can be trusted to hold the record.',
                'body' => 'We are asked to build blockchain systems more often than we build them. The assessment we apply is narrow and it disqualifies most candidates: does the requirement involve multiple organisations that must share a record, where no single one of them can be trusted to hold it, and where reconciliation between their separate records is currently a real operational cost?'."\n\n".'If a single organisation controls the record, a conventional database with append-only semantics, cryptographic audit logging and strong access control delivers the same integrity properties at a fraction of the operational cost. It is also far easier to hire for, to back up, to query, and to explain to an auditor.'."\n\n".'Where the consortium condition genuinely holds — multi-party settlement, shared provenance across a supply chain, asset registries with several independent participants — a permissioned ledger removes reconciliation as a category of work rather than automating it. That is a substantial prize. It is simply not the situation most organisations asking for a blockchain are actually in, and saying so is more useful to a client than building what was requested.',
                'read_minutes' => 5,
                'is_featured' => false,
                'published_at' => '2026-07-28 09:00:00',
            ],
            [
                'slug' => 'a-copilot-inherits-your-permission-model',
                'pillar_slug' => 'ai-automation',
                'format' => InsightFormat::EngineeringNote,
                'title' => 'A copilot inherits your permission model, or it breaks it',
                'abstract_line' => 'Retrieval that ignores per-user access turns an assistant into a data breach with a friendly interface.',
                'body' => 'The common architecture for an enterprise assistant is to index the organisation\'s documents into a vector store and retrieve against them at query time. The failure mode is immediate and severe: the index has no notion of who may read what, so the assistant will happily summarise a document the asking user could not open in the system it came from.'."\n\n".'This is not a content-filtering problem and it cannot be solved by instructing the model. It is an access control problem, and it has to be solved where access control is already solved — at retrieval, under the identity of the user asking.'."\n\n".'In practice that means the retrieval layer carries the user\'s effective permissions into the query, the index stores the access control metadata alongside each chunk, and permission changes in the source system propagate to the index on a defined and monitored interval. Where the source system\'s permission model is itself complex, that complexity has to be reproduced rather than approximated. It is a substantial amount of engineering, it is the part of the work that determines whether the assistant can be deployed at all, and it is routinely underestimated at proposal stage.',
                'read_minutes' => 4,
                'is_featured' => false,
                'published_at' => '2026-07-09 09:00:00',
            ],
        ];
    }
}
