<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_user_outside_allow_list_cannot_access_admin_dashboard(): void
    {
        config(['security.admin_allowed_emails' => ['allowed@example.com']]);

        $user = User::factory()->create([
            'email' => 'blocked@example.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_allowed_admin_user_can_access_admin_dashboard(): void
    {
        config(['security.admin_allowed_emails' => ['allowed@example.com']]);

        $user = User::factory()->create([
            'email' => 'allowed@example.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_local_environment_allows_admin_access_when_allow_list_is_empty(): void
    {
        config([
            'app.env' => 'local',
            'security.admin_allowed_emails' => [],
        ]);

        $user = User::factory()->create([
            'email' => 'local-admin@example.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk();
    }
}
