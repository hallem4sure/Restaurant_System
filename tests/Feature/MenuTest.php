<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\MenuSection;
use App\Models\MenuCategory;
use Spatie\Permission\Models\Role;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    public function test_admin_can_create_menu_section()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/admin/menu-sections', [
            'name' => 'Desserts',
            'is_active' => true,
        ]);

        $response->assertRedirect('/admin/menu-sections');
        $this->assertDatabaseHas('menu_sections', ['name' => 'Desserts']);
    }

    public function test_waiter_cannot_create_menu_section()
    {
        $waiter = User::factory()->create();
        $waiter->assignRole('waiter');

        $response = $this->actingAs($waiter)->post('/admin/menu-sections', [
            'name' => 'Desserts',
            'is_active' => true,
        ]);

        $response->assertForbidden();
    }
}
