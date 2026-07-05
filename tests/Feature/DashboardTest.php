<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-DASHBOARD-001: Menampilkan Dashboard
     * Menampilkan dashboard beserta informasi ringkasan data sesuai hak akses masing-masing role.
     */
    public function test_tc_dashboard_001_menampilkan_dashboard(): void
    {
        // 1. Superadmin
        $superadmin = User::factory()->create(['role' => 'Superadmin']);
        $response = $this->actingAs($superadmin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Penjualan Hari Ini');

        // 2. Admin
        $admin = User::factory()->create(['role' => 'Admin']);
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Penjualan Hari Ini');

        // 3. Kasir
        $kasir = User::factory()->create(['role' => 'Kasir']);
        $response = $this->actingAs($kasir)->get('/kasir/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Penjualan Hari Ini');
    }

    /**
     * TC-DASHBOARD-002: Akses Dashboard Tanpa Login
     * Mengarahkan guest ke halaman login saat mencoba mengakses dashboard secara langsung.
     */
    public function test_tc_dashboard_002_akses_dashboard_tanpa_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/');

        $responseAdmin = $this->get('/admin/dashboard');
        $responseAdmin->assertRedirect('/');

        $responseKasir = $this->get('/kasir/dashboard');
        $responseKasir->assertRedirect('/');
    }

    /**
     * TC-DASHBOARD-003: Menampilkan Dashboard Saat Data Kosong
     * Menampilkan data ringkasan bernilai 0 jika database kosong.
     */
    public function test_tc_dashboard_003_menampilkan_dashboard_saat_data_kosong(): void
    {
        // 1. Owner/Superadmin Dashboard
        $superadmin = User::factory()->create(['role' => 'Superadmin']);
        $response = $this->actingAs($superadmin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Rp 0');
        
        // 2. Kasir Dashboard
        $kasir = User::factory()->create(['role' => 'Kasir']);
        $response = $this->actingAs($kasir)->get('/kasir/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Rp 0');
    }
}
