<?php

namespace App\Admin;

use App\Enums\RfpTrack;
use App\Enums\SlaTier;
use App\Models\EngagementModel;
use App\Models\Industry;
use App\Models\Pillar;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * The content the console can edit that has no editor of its own.
 *
 * Six pillars, their services, the industries and the engagement models were
 * seeded at build time and then reachable only through the database. Everything
 * a visitor reads on a service page lived somewhere the person responsible for
 * it could not get at, which made the site read like a fixture rather than
 * something the business owns.
 *
 * They are declared here rather than given four near-identical controllers,
 * because the difference between them is entirely which fields they carry. The
 * form is rendered from these declarations and the request is validated from the
 * same ones, so a field cannot appear on screen without rules behind it.
 */
final class ContentType
{
    /**
     * @param  class-string<Model>  $model
     * @param  array<int, ContentField>  $fields
     */
    private function __construct(
        public string $key,
        public string $label,
        public string $subtitle,
        public string $model,
        public array $fields,
        public string $titleColumn = 'title',
    ) {}

    /** @return Collection<string, self> */
    public static function all(): Collection
    {
        return collect([
            self::pillars(),
            self::services(),
            self::industries(),
            self::engagementModels(),
        ])->keyBy(fn (self $t) => $t->key);
    }

    public static function find(string $key): self
    {
        $type = self::all()->get($key);

        abort_if($type === null, 404, "There is no editable content type called “{$key}”.");

        return $type;
    }

    /** @return Collection<int, Model> */
    public function records(): Collection
    {
        $query = $this->model::query();

        return method_exists($this->model, 'scopeOrdered') || in_array('ordered', get_class_methods($this->model), true)
            ? $query->ordered()->get()
            : $query->orderBy('sort_order')->get();
    }

    /** @return array<string, array<int, string>> */
    public function rules(?Model $existing = null): array
    {
        $rules = [];

        foreach ($this->fields as $field) {
            $set = $field->rules;

            // Slugs address a page, so a duplicate would silently shadow another
            // record. The uniqueness rule has to know which row to ignore.
            if ($field->name === 'slug') {
                $table = (new $this->model)->getTable();
                $set[] = $existing
                    ? 'unique:'.$table.',slug,'.$existing->getKey()
                    : 'unique:'.$table.',slug';
            }

            $rules[$field->name] = $set;
        }

        return $rules;
    }

    // --- the types -----------------------------------------------------------

    private static function pillars(): self
    {
        return new self(
            'pillars',
            'Pillars',
            'The six disciplines the practice is organised around. Each one owns the services beneath it, so renaming a pillar changes the navigation and the URL of every service under it.',
            Pillar::class,
            [
                ContentField::text('title', 'Title')->required(),
                ContentField::text('nav_title', 'Navigation title', ['nullable', 'string', 'max:120'], 'The shorter form used in the menu. Falls back to the title.'),
                ContentField::text('slug', 'Slug', ['required', 'string', 'max:120', 'regex:/^[a-z0-9-]+$/'], 'Lower case, hyphens only. It is part of the page address.'),
                ContentField::number('number', 'Number', ['required', 'integer', 'min:1', 'max:99'], 'The 01–06 marker shown on the pillar card.'),
                ContentField::textarea('descriptor', 'Descriptor', ['required', 'string', 'max:400'], 'One line under the title, in the navigation and on cards.')->required(),
                ContentField::textarea('thesis', 'Thesis', ['required', 'string', 'max:2000'], 'The argument for why this discipline exists. Shown at the head of the pillar page.')->required(),
                ContentField::number('sort_order', 'Sort order'),
            ],
        );
    }

    private static function services(): self
    {
        return new self(
            'services',
            'Services',
            'The individual offers under each pillar. This is the copy a procurement reviewer reads before deciding whether to send an RFP, so it should say what is delivered rather than describe a category.',
            Service::class,
            [
                ContentField::select('pillar_id', 'Pillar', Pillar::query()->ordered()->pluck('title', 'id')->all(),
                    help: 'Which discipline this service sits under. Changing it changes the page address.'),
                ContentField::text('title', 'Title')->required(),
                ContentField::text('slug', 'Slug', ['required', 'string', 'max:120', 'regex:/^[a-z0-9-]+$/']),
                ContentField::textarea('executive_summary', 'Executive summary', ['required', 'string', 'max:2000'], 'Two or three sentences. What it is and what it is for.')->required(),
                ContentField::lines('outcomes', 'Outcomes', 'What the client ends up with. One per line, written as results rather than activities.'),
                ContentField::lines('capabilities', 'Capabilities', 'What we do to get there. One per line.'),
                ContentField::lines('stack', 'Stack', 'Named technologies. One per line.'),
                ContentField::textarea('architecture_note', 'Architecture note', ['nullable', 'string', 'max:2000'], 'The technical position that distinguishes how we build this.'),
                ContentField::select('sla_tier', 'Service tier', collect(SlaTier::cases())->mapWithKeys(fn (SlaTier $t) => [$t->value => $t->label()])->all(), required: false),
                ContentField::lines('compliance_tags', 'Compliance tags', 'Standards this service is delivered against. One per line.'),
                ContentField::text('meta_title', 'Meta title', ['nullable', 'string', 'max:70'], 'Search result title. Under 70 characters or it is truncated.'),
                ContentField::textarea('meta_description', 'Meta description', ['nullable', 'string', 'max:160'], 'Search result summary. Under 160 characters.'),
                ContentField::number('sort_order', 'Sort order'),
            ],
        );
    }

    private static function industries(): self
    {
        return new self(
            'industries',
            'Industries',
            'The sectors we claim to understand. Each one states the constraint that sector actually works under — that constraint is the reason a buyer believes the rest of the page.',
            Industry::class,
            [
                ContentField::text('title', 'Title')->required(),
                ContentField::text('slug', 'Slug', ['required', 'string', 'max:120', 'regex:/^[a-z0-9-]+$/']),
                ContentField::textarea('constraint_statement', 'Constraint', ['required', 'string', 'max:2000'], 'The thing that makes building for this sector different. Be specific enough that someone in it would recognise it.')->required(),
                ContentField::lines('pressures', 'Pressures', 'What the sector is currently under. One per line.'),
                ContentField::textarea('regulatory_notes', 'Regulatory notes', ['nullable', 'string', 'max:2000'], 'The named regimes that apply. Do not cite a regulation you have not read.'),
                ContentField::number('sort_order', 'Sort order'),
            ],
        );
    }

    private static function engagementModels(): self
    {
        return new self(
            'engagement-models',
            'Engagement models',
            'How work is contracted. Each model states its commercial basis and, more usefully to a buyer, where its scope stops.',
            EngagementModel::class,
            [
                ContentField::text('title', 'Title')->required(),
                ContentField::text('slug', 'Slug', ['required', 'string', 'max:120', 'regex:/^[a-z0-9-]+$/']),
                ContentField::text('reference', 'Reference', ['nullable', 'string', 'max:40'], 'The short code used in proposals.'),
                ContentField::textarea('summary', 'Summary', ['required', 'string', 'max:2000'])->required(),
                ContentField::textarea('commercial_basis', 'Commercial basis', ['required', 'string', 'max:2000'], 'How it is priced and billed.')->required(),
                ContentField::textarea('scope_boundary', 'Scope boundary', ['required', 'string', 'max:2000'], 'What this model does not cover. The most useful paragraph on the page.')->required(),
                ContentField::text('governance_cadence', 'Governance cadence', ['nullable', 'string', 'max:255'], 'How often the client sees progress and who attends.'),
                ContentField::text('suited_to', 'Suited to', ['nullable', 'string', 'max:255']),
                ContentField::select('rfp_track', 'RFP track', collect(RfpTrack::cases())->mapWithKeys(fn (RfpTrack $t) => [$t->value => $t->label()])->all(), required: false,
                    help: 'Which track the proposal form pre-selects when someone arrives from this model.'),
                ContentField::number('sort_order', 'Sort order'),
            ],
        );
    }
}
