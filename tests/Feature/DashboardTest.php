<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        $this->artisan('db:seed', ['--class' => 'SettingsSeeder']);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        return $admin;
    }

    #[Test]
    public function admin_can_view_dashboard()
    {
        $admin = $this->createAdmin();

        // Create some dummy data to ensure the dashboard loads without errors
        RestaurantTable::create(['table_number' => 'T1', 'capacity' => 2, 'status' => 'available']);
        
        $order = Order::create([
            'order_number' => 'ORD-123',
            'status' => 'pending',
            'subtotal' => 10,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'service_charge_rate' => 0,
            'service_charge_amount' => 0,
            'total_amount' => 10,
        ]);

        Bill::create([
            'bill_number' => 'BILL-123',
            'order_id' => $order->id,
            'status' => 'paid',
            'subtotal' => 10,
            'tax_amount' => 0,
            'service_charge_amount' => 0,
            'total_amount' => 10,
            'amount_paid' => 10,
            'change_amount' => 0,
            'paid_at' => now(),
            'payment_method' => 'cash',
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        
        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHasAll([
            'stats',
            'revenueChart',
            'ordersStatusChart',
            'topMenuItems',
            'recentOrders',
            'recentReservations'
        ]);
    }

    #[Test]
    public function dashboard_stats_are_calculated_correctly()
    {
        $admin = $this->createAdmin();

        $table = RestaurantTable::create(['table_number' => 'T1', 'capacity' => 2, 'status' => 'occupied']);
        $table2 = RestaurantTable::create(['table_number' => 'T2', 'capacity' => 4, 'status' => 'available']);
        
        $order = Order::create([
            'order_number' => 'ORD-123',
            'status' => 'pending',
            'subtotal' => 10,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'service_charge_rate' => 0,
            'service_charge_amount' => 0,
            'total_amount' => 10,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        
        $response->assertOk();
        $stats = $response->viewData('stats');
        
        $this->assertEquals(1, $stats['ordersToday']);
        $this->assertEquals(1, $stats['pendingOrders']);
        $this->assertEquals(2, $stats['totalTables']);
        $this->assertEquals(1, $stats['occupiedTables']);
        $this->assertEquals(1, $stats['availableTables']);
    }
}
