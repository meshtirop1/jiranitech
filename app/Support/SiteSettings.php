<?php

namespace App\Support;

use App\Enums\SlaTier;
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
        'company_google_verification' => 'company.verification.google',
        'company_google_verification_file' => 'company.verification.google_file',
        'company_bing_verification' => 'company.verification.bing',
        'company_response_acknowledgement' => 'company.response.acknowledgement',
        'company_response_substantive' => 'company.response.substantive',
    ];

    /**
     * Service level tiers, built from the enum's own field list.
     *
     * Generated rather than typed out so a new field on a tier cannot be added
     * to the form and then silently fail to persist.
     *
     * @return array<string, string>
     */
    public static function slaMap(): array
    {
        $map = [];

        foreach (SlaTier::cases() as $tier) {
            foreach (array_keys(SlaTier::fields()) as $field) {
                $map['sla_'.$tier->value.'_'.$field] = 'sla.tiers.'.$tier->value.'.'.$field;
            }

            $map['sla_'.$tier->value.'_service_credits'] = 'sla.tiers.'.$tier->value.'.service_credits';
        }

        return $map;
    }

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

        // Service credits are a yes/no, so "0" is a real answer rather than an
        // absent one and cannot be filtered out with filled().
        foreach (self::slaMap() as $settingKey => $configKey) {
            if (! array_key_exists($settingKey, $stored)) {
                continue;
            }

            config([$configKey => str_ends_with($settingKey, '_service_credits')
                ? (bool) $stored[$settingKey]
                : $stored[$settingKey]]);
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
