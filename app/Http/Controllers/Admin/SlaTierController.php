<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SlaTier;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The service level tiers, and whether they may be quoted as commitments.
 *
 * These were constants in an enum, so correcting a commercial promise meant a
 * code change and a deploy on a host with no shell. Every figure on this screen
 * is something a client can hold the firm to, which makes it the wrong kind of
 * thing to keep in code.
 *
 * Ratification is deliberately a separate act from editing. Changing a target is
 * routine; declaring that the targets are achievable on the infrastructure
 * underneath and contractually bound is a decision somebody owns, and it unlocks
 * the site quoting them as commitments rather than as a framework.
 */
class SlaTierController extends Controller
{
    public function edit(): View
    {
        return view('admin.sla.edit', [
            'tiers' => SlaTier::cases(),
            'fields' => SlaTier::fields(),
            'ratified' => SlaTier::ratified(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [];

        foreach (SlaTier::cases() as $tier) {
            foreach (array_keys(SlaTier::fields()) as $field) {
                $rules[$this->key($tier, $field)] = ['required', 'string', 'max:120'];
            }

            $rules[$this->key($tier, 'service_credits')] = ['boolean'];
        }

        $data = $request->validate($rules);

        foreach (SlaTier::cases() as $tier) {
            foreach (array_keys(SlaTier::fields()) as $field) {
                Setting::put($this->key($tier, $field), $data[$this->key($tier, $field)], 'sla');
            }

            Setting::put(
                $this->key($tier, 'service_credits'),
                $request->boolean($this->key($tier, 'service_credits')) ? '1' : '0',
                'sla',
            );
        }

        // Editing a target invalidates any previous ratification: the figures
        // that were confirmed are not the figures now published. Whoever owns
        // that decision confirms the new ones.
        $wasRatified = SlaTier::ratified();
        $ratifying = $request->boolean('ratified');

        if ($wasRatified && ! $ratifying) {
            Setting::put('gate_g05_ratified', '', 'gates');
        } elseif ($ratifying) {
            Setting::put('gate_g05_ratified', '1', 'gates');
        }

        Setting::flush();

        return back()->with('status', $ratifying
            ? 'Service levels saved and ratified. The site may now quote them as commitments.'
            : 'Service levels saved. They are published as a framework until they are ratified.');
    }

    private function key(SlaTier $tier, string $field): string
    {
        return 'sla_'.$tier->value.'_'.$field;
    }
}
