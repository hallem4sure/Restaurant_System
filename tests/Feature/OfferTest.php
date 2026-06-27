<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\Offer;

class OfferTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name'       => 'Test Offer',
            'type'       => 'percentage',
            'value'      => 10,
            'is_active'  => 1,
            'starts_at'  => now()->format('Y-m-d\TH:i'),
            'ends_at'    => now()->addMonth()->format('Y-m-d\TH:i'),
        ], $overrides);
    }

    #[Test]
    public function admin_can_create_percentage_offer()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/admin/offers', $this->validPayload());

        $response->assertRedirect('/admin/offers');
        $this->assertDatabaseHas('offers', ['name' => 'Test Offer', 'type' => 'percentage']);
    }

    #[Test]
    public function admin_can_create_fixed_offer()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/admin/offers', $this->validPayload([
            'name'  => 'Fixed Offer',
            'type'  => 'fixed',
            'value' => 5,
        ]));

        $response->assertRedirect('/admin/offers');
        $this->assertDatabaseHas('offers', ['name' => 'Fixed Offer', 'type' => 'fixed']);
    }

    #[Test]
    public function offer_creation_fails_if_end_date_before_start_date()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/admin/offers', $this->validPayload([
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'ends_at'   => now()->subDay()->format('Y-m-d\TH:i'),
        ]));

        $response->assertSessionHasErrors('ends_at');
    }

    #[Test]
    public function offer_creation_fails_if_percentage_over_100()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/admin/offers', $this->validPayload([
            'type'  => 'percentage',
            'value' => 150,
        ]));

        $response->assertSessionHasErrors('value');
    }

    #[Test]
    public function admin_can_update_offer()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $offer = Offer::create($this->validPayload([
            'starts_at' => now(),
            'ends_at'   => now()->addMonth(),
            'is_active' => true,
        ]));

        $response = $this->actingAs($admin)->put("/admin/offers/{$offer->id}", $this->validPayload([
            'name' => 'Updated Offer Name',
        ]));

        $response->assertRedirect('/admin/offers');
        $this->assertDatabaseHas('offers', ['id' => $offer->id, 'name' => 'Updated Offer Name']);
    }

    #[Test]
    public function admin_can_delete_offer()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $offer = Offer::create($this->validPayload([
            'starts_at' => now(),
            'ends_at'   => now()->addMonth(),
            'is_active' => true,
        ]));

        $response = $this->actingAs($admin)->delete("/admin/offers/{$offer->id}");

        $response->assertRedirect('/admin/offers');
        $this->assertDatabaseMissing('offers', ['id' => $offer->id]);
    }

    #[Test]
    public function waiter_cannot_manage_offers()
    {
        $waiter = User::factory()->create();
        $waiter->assignRole('waiter');

        $response = $this->actingAs($waiter)->post('/admin/offers', $this->validPayload());

        $response->assertForbidden();
    }
}
