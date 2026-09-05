<?php

namespace Tests\Feature;

use App\Models\Pillar;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_taxonomy_seeds_six_pillars_and_twenty_two_service_lines(): void
    {
        $this->seed();

        $this->assertSame(6, Pillar::query()->count());
        $this->assertSame(22, Service::query()->count());
    }

    public function test_every_service_page_renders_the_full_t04_section_set(): void
    {
        $this->seed();

        $service = Service::query()->where('slug', 'transaction-processing-pci-dss')->with('pillar')->sole();

        $this->get(route('services.show', [$service->pillar, $service]))
            ->assertOk()
            ->assertSee($service->title)
            ->assertSee('What you get from the engagement')
            ->assertSee('What we do inside it')
            ->assertSee('Architectural position')
            ->assertSee('Representative technologies')
            ->assertSee('SLA, compliance and governance')
            ->assertSee('PCI-DSS v4.0');
    }

    public function test_a_service_cannot_be_reached_through_the_wrong_pillar(): void
    {
        $this->seed();

        $pillar = Pillar::query()->where('slug', 'ai-automation')->sole();
        $service = Service::query()->where('slug', 'transaction-processing-pci-dss')->sole();

        $this->get("/services/{$pillar->slug}/{$service->slug}")->assertNotFound();
    }

    public function test_all_twenty_two_service_pages_respond(): void
    {
        $this->seed();

        Service::query()->with('pillar')->get()->each(function (Service $service): void {
            $this->get(route('services.show', [$service->pillar, $service]))
                ->assertOk()
                ->assertSee($service->title);
        });
    }

    public function test_the_taxonomy_index_lists_every_pillar(): void
    {
        $this->seed();

        $response = $this->get(route('services.index'))->assertOk();

        Pillar::query()->get()->each(fn (Pillar $pillar) => $response->assertSee($pillar->title));
    }
}
