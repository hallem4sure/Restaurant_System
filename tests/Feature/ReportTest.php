<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReportTest extends TestCase
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

    private function createWaiter(): User
    {
        $waiter = User::factory()->create();
        $waiter->assignRole('waiter');
        return $waiter;
    }

    #[Test]
    public function admin_can_view_reports_page()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertViewIs('admin.reports.index');
        $response->assertViewHasAll(['kpis', 'salesData', 'activeTab', 'startDate', 'endDate']);
    }

    #[Test]
    public function waiter_cannot_view_reports_page()
    {
        $waiter = $this->createWaiter();

        $response = $this->actingAs($waiter)->get(route('admin.reports.index'));

        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_export_csv_report()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.reports.export', ['type' => 'sales']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=report_sales_' . now()->startOfMonth()->format('Y-m-d') . '_' . now()->endOfMonth()->format('Y-m-d') . '.csv');
    }

    #[Test]
    public function admin_can_view_print_page()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.reports.print', ['tab' => 'revenue']));

        $response->assertOk();
        $response->assertViewIs('admin.reports.print');
        $response->assertViewHas('data');
    }
}
