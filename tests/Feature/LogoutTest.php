<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-LOGOUT-001: Logout Berhasil
     * Menghapus sesi dan mengarahkan pengguna kembali ke halaman login.
     */
    public function test_tc_logout_001_logout_berhasil(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $response = $this->actingAs($user)
            ->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * TC-LOGOUT-002: Membatalkan Logout
     * Sesi tetap dipertahankan dan pengguna tetap berstatus Logged In jika logout dibatalkan.
     */
    public function test_tc_logout_002_membatalkan_logout(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $this->actingAs($user);
        
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * TC-LOGOUT-003: Akses Dashboard Setelah Logout (Back Browser)
     * Pengguna tidak bisa mengakses dashboard kembali via history browser setelah logout.
     */
    public function test_tc_logout_003_akses_dashboard_setelah_logout(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $this->actingAs($user);
        $this->post('/logout');
        
        $response = $this->get('/dashboard');
        $response->assertRedirect('/');
    }
}
