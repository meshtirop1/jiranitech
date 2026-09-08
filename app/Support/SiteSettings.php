<?php

namespace App\Support;

use App\Models\Setting;
use Throwable;

/**
 * Overlays database-held settings onto the config the templates already read.
 *
 * The deployment target has no shell, so the account owner cannot edit .env. Anything
 * an administrator changes in the console is written to the settings table and applied
 * here, which is what lets gate G-07 be closed from a browser.
 *
 * Applied once per request during AppServiceProvider::boot(). It is a separate class
 * rather than a private provider method so the mechanism can be exercised directly in
 * a test without standing up a whole new application.
 */
class SiteSettings
{
    /**
     * Setting key => config key.
     *
     * @var array<string, string>
     */
    public const MAP = [
        'company_legal_name' => 'company.legal_name',
        'company_parent_name' => 'company.parent.name',
        'company_parent_url' => 'company.parent.url',
        'company_parent_registration_number' => 'company.parent.registration_number',
        'company_parent_tax_pin' => 'company.parent.tax_pin',
        'company_marketplace_name' => 'company.marketplace.name',
        'company_marketplace_url' => 'company.marketplace.url',
        'company_registered_address' => 'company.registered_address',
        'company_postal_address' => 'company.postal_address',
        'company_city' => 'company.city',
        'company_country' => 'company.country',
        'company_office_hours' => 'company.office_hours',
        'company_email_enquiries' => 'company.email.enquiries',
        'company_email_rfp' => 'company.email.rfp',
        'company_email_security' => 'company.email.security',
        'company_email_careers' => 'company.email.careers',
        'company_email_privacy' => 'company.email.privacy',
        'company_telephone' => 'company.telephone',
        'company_client_portal_url' => 'company.client_portal_url',
        'company_response_acknowledgement' => 'company.response.acknowledgement',
        'company_response_substantive' => 'company.response.substantive',
    ];

    /**
     * Wrapped defensively: this runs during `migrate` and `config:cache` too, when the
     * settings table may not exist yet, and a failure there must not break the console
     * that fixes it.
     */
    public static function applyToConfig(): void
    {
        try {
            $stored = Setting::values();
        } catch (Throwable) {
            return;
        }

        if ($stored === []) {
            return;
        }

        foreach (self::MAP as $settingKey => $configKey) {
            if (filled($stored[$settingKey] ?? null)) {
                config([$configKey => $stored[$settingKey]]);
            }
        }

        // Two derived values have to be rebuilt after the parent name lands.
        if (filled($stored['company_parent_name'] ?? null)) {
            config([
                'company.parent_name' => $stored['company_parent_name'],
                'company.division_line' => 'A division of '.$stored['company_parent_name'],
            ]);
        }
    }
}
