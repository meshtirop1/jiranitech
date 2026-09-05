<?php

namespace Database\Factories;

use App\Enums\RfpTrack;
use App\Models\RfpSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<RfpSubmission>
 */
class RfpSubmissionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => 'JTS-RFP-'.now()->format('Ymd').'-'.Str::upper(Str::random(5)),
            'track' => fake()->randomElement(RfpTrack::cases())->value,
            'organisation' => fake()->company(),
            'contact_name' => fake()->name(),
            'role' => fake()->jobTitle(),
            'email' => fake()->companyEmail(),
            'telephone' => '+254 700 '.fake()->numerify('######'),
            'country' => 'Kenya',
            'budget_band' => 'USD 150,000 – 500,000',
            'timeline' => 'This quarter',
            'scope_summary' => fake()->paragraph(4),
            'service_interests' => ['cloud-devops'],
            'nda_required' => fake()->boolean(30),
            'source_page' => '/contact/request-for-proposal',
            'submitted_ip' => fake()->ipv4(),
            'acknowledged_at' => null,
        ];
    }

    public function acknowledged(): static
    {
        return $this->state(fn () => ['acknowledged_at' => now()]);
    }
}
