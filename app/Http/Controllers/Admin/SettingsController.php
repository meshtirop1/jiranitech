<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Corporate identity and the two gates that are a judgement call rather than a data
 * state. Everything here writes to the settings table, which config/company.php reads
 * ahead of .env, so the account owner can close gate G-07 without shell access.
 */
class SettingsController extends Controller
{
    /**
     * field => [label, hint, type]
     *
     * @var array<string, array{0: string, 1: string, 2: string}>
     */
    public const FIELDS = [
        'company_legal_name' => ['Division name', 'How this business unit is named across the site.', 'text'],
        'company_parent_name' => ['Holding company', 'The registered entity. Shown in the footer and the affiliation line.', 'text'],
        'company_parent_registration_number' => ['Company registration number', 'From the certificate of incorporation. Attributed to the holding company, not to this division.', 'text'],
        'company_parent_tax_pin' => ['KRA PIN', 'From the PIN certificate. Also the holding company\'s, and also what Kenyan procurement asks for. Clear it to remove it from the footer.', 'text'],
        'company_parent_url' => ['Holding company website', 'Leave empty until one exists. "Investor & Group" then links to this site\'s own group section rather than to the marketplace.', 'url'],
        'company_marketplace_name' => ['Marketplace name', 'A sibling platform operated by the group, not the parent.', 'text'],
        'company_marketplace_url' => ['Marketplace website', 'Only ever linked from its own platform page.', 'url'],
        'company_registered_address' => ['Registered office', 'Required before launch. Street-level line only — the city and country below are added to it. Take it from the CR12, not the certificate of incorporation.', 'text'],
        'company_postal_address' => ['Postal address', 'The box correspondence goes to, if it differs from the registered office.', 'text'],
        'company_city' => ['Operating city', '', 'text'],
        'company_country' => ['Country', '', 'text'],
        'company_office_hours' => ['Office hours', '', 'text'],
        'company_email_enquiries' => ['Enquiries address', 'Shown in the footer and used for job applications.', 'email'],
        'company_email_rfp' => ['RFP address', '', 'email'],
        'company_email_security' => ['Security disclosure address', 'Published in the responsible disclosure policy.', 'email'],
        'company_telephone' => ['Telephone', '', 'text'],
        'company_client_portal_url' => ['Client portal URL', 'The portal link only appears in the utility bar once this is set.', 'url'],
        'company_response_acknowledgement' => ['Acknowledgement time', 'A commercial promise. It appears in the RFP microcopy sitewide.', 'text'],
        'company_response_substantive' => ['Substantive reply time', 'A commercial promise. It appears in the hero and the RFP band.', 'text'],
    ];

    public function edit(): View
    {
        // Prefill from the value the site is actually using, not only from the settings
        // table. Most of these still come from .env on a fresh install, and showing
        // blanks would invite an administrator to save over them with nothing.
        $values = [];
        foreach (array_keys(self::FIELDS) as $key) {
            $configKey = SiteSettings::MAP[$key] ?? null;
            $values[$key] = Setting::get($key, $configKey ? config($configKey) : null);
        }

        return view('admin.settings', [
            'fields' => self::FIELDS,
            'values' => $values,
            'gateG05' => (bool) Setting::get('gate_g05_ratified'),
            'gateG06' => (bool) Setting::get('gate_g06_counsel_signed_off'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [];
        foreach (self::FIELDS as $key => [$label, $hint, $type]) {
            $rules[$key] = match ($type) {
                'email' => ['nullable', 'email', 'max:255'],
                'url' => ['nullable', 'url', 'max:255'],
                default => ['nullable', 'string', 'max:255'],
            };
        }

        $validated = $request->validate($rules);

        foreach ($validated as $key => $value) {
            Setting::put($key, $value, 'company');
        }

        Setting::put('gate_g05_ratified', $request->boolean('gate_g05_ratified') ? '1' : '', 'gates');
        Setting::put('gate_g06_counsel_signed_off', $request->boolean('gate_g06_counsel_signed_off') ? '1' : '', 'gates');

        return back()->with('status', 'Settings saved. They take effect across the site immediately.');
    }
}
