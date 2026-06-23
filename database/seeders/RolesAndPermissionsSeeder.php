<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Permission names and role assignments match the approved
     * implementation plan permission matrix exactly.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Define all permissions (exact names from the approved plan) ────────
        $permissions = [
            // Users
            'manage users',

            // Menu
            'view menu',
            'manage menu',

            // Tables
            'view tables',
            'manage tables',
            'update table status',

            // Offers
            'manage offers',

            // Reservations
            'view reservations',
            'create reservations',
            'update reservation status',

            // Orders
            'view orders',
            'create orders',
            'update order status',
            'cancel orders',

            // Kitchen
            'view kitchen',
            'update kitchen status',

            // Billing
            'view bills',
            'create bills',
            'process payments',

            // Reports
            'view reports',

            // Settings
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // ── 1. Admin — all permissions ─────────────────────────────────────────
        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions(Permission::all());

        // ── 2. Waiter ──────────────────────────────────────────────────────────
        $waiter = Role::findOrCreate('waiter', 'web');
        $waiter->syncPermissions([
            'view menu',
            'view tables',
            'update table status',
            'view reservations',
            'create reservations',
            'update reservation status',
            'view orders',
            'create orders',
            'update order status',
            'cancel orders',
        ]);

        // ── 3. Cashier ─────────────────────────────────────────────────────────
        $cashier = Role::findOrCreate('cashier', 'web');
        $cashier->syncPermissions([
            'view menu',
            'view tables',
            'view reservations',
            'view orders',
            'view bills',
            'create bills',
            'process payments',
            'view reports',
        ]);

        // ── 4. Kitchen Staff ───────────────────────────────────────────────────
        $kitchenStaff = Role::findOrCreate('kitchen_staff', 'web');
        $kitchenStaff->syncPermissions([
            'view menu',
            'view orders',
            'update order status',
            'view kitchen',
            'update kitchen status',
        ]);
    }
}
