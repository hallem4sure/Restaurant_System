<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantTable;

class TableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    public function test_admin_can_manage_tables()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/admin/tables', [
            'table_number' => 'TEST-1',
            'capacity' => 4,
            'status' => 'available',
            'is_private' => false,
        ]);

        $response->assertRedirect('/admin/tables');
        $this->assertDatabaseHas('restaurant_tables', [
            'table_number' => 'TEST-1',
            'capacity' => 4
        ]);
    }

    public function test_waiter_cannot_manage_tables()
    {
        $waiter = User::factory()->create();
        $waiter->assignRole('waiter');

        // Note: The route itself might be blocked by 'role:admin' middleware
        // or by FormRequest validation. In either case, it should be forbidden or not found.
        $response = $this->actingAs($waiter)->post('/admin/tables', [
            'table_number' => 'TEST-2',
            'capacity' => 4,
            'status' => 'available',
            'is_private' => false,
        ]);

        // We expect a 403 Forbidden
        $response->assertForbidden();
    }
}
