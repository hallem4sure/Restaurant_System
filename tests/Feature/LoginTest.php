<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_redirects_to_admin_dashboard()
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'admin@restaurant.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('admin/dashboard');
    }
}
