<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Bill;
use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\MenuCategory;
use App\Models\RestaurantTable;

class BillTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    private function createServedOrder(): Order
    {
        $section  = MenuSection::create(['name' => 'Main', 'is_active' => true]);
        $category = MenuCategory::create(['section_id' => $section->id, 'name' => 'Mains', 'is_active' => true]);
        $item     = MenuItem::create(['category_id' => $category->id, 'name' => 'Steak', 'price' => 25.00, 'is_available' => true]);
        $table    = RestaurantTable::create(['table_number' => 1, 'capacity' => 4, 'status' => 'occupied']);

        $order = Order::create([
            'order_number'          => 'ORD-TEST-001',
            'table_id'              => $table->id,
            'type'                  => 'walk_in',
            'status'                => 'served',
            'subtotal'              => 25.00,
            'discount_amount'       => 0,
            'tax_rate'              => 5.00,
            'tax_amount'            => 1.25,
            'service_charge_rate'   => 10.00,
            'service_charge_amount' => 2.50,
            'total_amount'          => 28.75,
        ]);

        $order->items()->create([
            'menu_item_id'   => $item->id,
            'quantity'       => 1,
            'unit_price'     => 25.00,
            'subtotal'       => 25.00,
            'kitchen_status' => 'ready',
        ]);

        return $order;
    }

    #[Test]
    public function cashier_can_view_bills_index()
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $response = $this->actingAs($cashier)->get('/admin/bills');
        $response->assertOk();
    }

    #[Test]
    public function waiter_cannot_view_bills_index()
    {
        $waiter = User::factory()->create();
        $waiter->assignRole('waiter');

        $response = $this->actingAs($waiter)->get('/admin/bills');
        $response->assertForbidden();
    }

    #[Test]
    public function cashier_can_generate_bill_from_order()
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $order = $this->createServedOrder();

        $response = $this->actingAs($cashier)->post('/admin/bills', [
            'order_id' => $order->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bills', [
            'order_id' => $order->id,
            'status'   => 'pending',
            'total_amount' => 28.75,
        ]);
    }

    #[Test]
    public function duplicate_bill_generation_returns_existing_bill()
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $order = $this->createServedOrder();

        // Generate twice
        $this->actingAs($cashier)->post('/admin/bills', ['order_id' => $order->id]);
        $this->actingAs($cashier)->post('/admin/bills', ['order_id' => $order->id]);

        $this->assertDatabaseCount('bills', 1);
    }

    #[Test]
    public function cashier_can_process_cash_payment()
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $order = $this->createServedOrder();

        // Generate bill first
        $this->actingAs($cashier)->post('/admin/bills', ['order_id' => $order->id]);
        $bill = Bill::first();

        $response = $this->actingAs($cashier)->put("/admin/bills/{$bill->id}", [
            'payment_method' => 'cash',
            'amount_paid'    => 30.00,
            'notes'          => 'Paid by customer',
        ]);

        $response->assertRedirect("/admin/bills/{$bill->id}");
        $this->assertDatabaseHas('bills', [
            'id'             => $bill->id,
            'status'         => 'paid',
            'payment_method' => 'cash',
            'amount_paid'    => 30.00,
            'change_amount'  => 1.25,
        ]);

        // Order should be completed
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'completed']);
    }

    #[Test]
    public function payment_rejected_when_amount_is_less_than_total()
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $order = $this->createServedOrder();
        $this->actingAs($cashier)->post('/admin/bills', ['order_id' => $order->id]);
        $bill = Bill::first();

        $response = $this->actingAs($cashier)->put("/admin/bills/{$bill->id}", [
            'payment_method' => 'cash',
            'amount_paid'    => 10.00, // less than 28.75
        ]);

        $response->assertSessionHasErrors('amount_paid');
        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'pending']);
    }

    #[Test]
    public function waiter_cannot_process_payment()
    {
        $admin  = User::factory()->create();
        $admin->assignRole('admin');
        $waiter = User::factory()->create();
        $waiter->assignRole('waiter');

        $order = $this->createServedOrder();
        $this->actingAs($admin)->post('/admin/bills', ['order_id' => $order->id]);
        $bill = Bill::first();

        $response = $this->actingAs($waiter)->put("/admin/bills/{$bill->id}", [
            'payment_method' => 'cash',
            'amount_paid'    => 30.00,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'pending']);
    }

    #[Test]
    public function cashier_can_view_invoice()
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $order = $this->createServedOrder();
        $this->actingAs($cashier)->post('/admin/bills', ['order_id' => $order->id]);
        $bill = Bill::first();

        $response = $this->actingAs($cashier)->get("/admin/bills/{$bill->id}");
        $response->assertOk();
        $response->assertSee($bill->bill_number);
    }

    #[Test]
    public function admin_can_cancel_pending_bill()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $order = $this->createServedOrder();
        $this->actingAs($admin)->post('/admin/bills', ['order_id' => $order->id]);
        $bill = Bill::first();

        $response = $this->actingAs($admin)->delete("/admin/bills/{$bill->id}");
        $response->assertRedirect('/admin/bills');
        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'cancelled']);
    }

    #[Test]
    public function cannot_cancel_paid_bill()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $order = $this->createServedOrder();
        $this->actingAs($admin)->post('/admin/bills', ['order_id' => $order->id]);
        $bill = Bill::first();

        // Pay it first
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');
        $this->actingAs($cashier)->put("/admin/bills/{$bill->id}", [
            'payment_method' => 'card',
            'amount_paid'    => 28.75,
        ]);

        // Now try to cancel
        $response = $this->actingAs($admin)->delete("/admin/bills/{$bill->id}");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'status' => 'paid']);
    }
}
