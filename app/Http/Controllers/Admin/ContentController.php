<?php

namespace App\Http\Controllers\Admin;

use App\Admin\ContentType;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Editing the content that describes what the firm sells.
 *
 * One controller for four types, because the only thing that differs between
 * them is the field list, and that is declared in ContentType. See the note
 * there for why this is not four controllers.
 */
class ContentController extends Controller
{
    public function index(string $type): View
    {
        $content = ContentType::find($type);

        return view('admin.content.index', [
            'type' => $content,
            'records' => $content->records(),
            'types' => ContentType::all(),
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $content = ContentType::find($type);
        $data = $request->validate($content->rules());

        $record = $content->model::create($this->attributes($content, $data, $request));

        return back()->with('status', $this->name($content, $record).' created.');
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        $content = ContentType::find($type);
        $record = $content->model::findOrFail($id);

        $data = $request->validate($content->rules($record));

        $record->update($this->attributes($content, $data, $request));

        return back()->with('status', $this->name($content, $record).' saved.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $content = ContentType::find($type);
        $record = $content->model::findOrFail($id);

        // A pillar carries services, and deleting one would leave them pointing
        // at a discipline that no longer exists — which is a broken page rather
        // than a tidy-up. Say so instead of cascading silently.
        if (method_exists($record, 'services') && $record->services()->exists()) {
            return back()->withErrors([
                'delete' => $this->name($content, $record).' still has '.$record->services()->count()
                    .' service(s) under it. Move or delete those first.',
            ]);
        }

        $name = $this->name($content, $record);
        $record->delete();

        return back()->with('status', $name.' deleted.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function attributes(ContentType $content, array $data, Request $request): array
    {
        $attributes = [];

        foreach ($content->fields as $field) {
            $attributes[$field->name] = $field->type === 'checkbox'
                ? $request->boolean($field->name)
                : $field->toModel($data[$field->name] ?? null);
        }

        return $attributes;
    }

    private function name(ContentType $content, Model $record): string
    {
        return '“'.($record->{$content->titleColumn} ?? 'Record').'”';
    }
}
