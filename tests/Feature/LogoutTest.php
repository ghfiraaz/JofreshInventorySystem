<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Logout Berhasil
     * Menghapus sesi dan mengarahkan pengguna kembali ke halaman login.
     */
    public function test_logout_berhasil(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $response = $this->actingAs($user)
            ->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Membatalkan Logout
     * Sesi tetap dipertahankan dan pengguna tetap berstatus Logged In jika logout dibatalkan.
     */
    public function test_membatalkan_logout(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $this->actingAs($user);
        
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Akses Dashboard Setelah Logout
     * Pengguna tidak bisa mengakses dashboard kembali via history browser setelah logout.
     */
    public function test_akses_dashboard_setelah_logout(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $this->actingAs($user);
        $this->post('/logout');
        
        $response = $this->get('/dashboard');
        $response->assertRedirect('/');
    }
}
