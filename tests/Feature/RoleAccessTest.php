<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    // ========================================================
    // AKSES TANPA LOGIN (GUEST)
    // ========================================================

    public function test_guest_tidak_bisa_akses_dashboard_superadmin(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/');
    }

    public function test_guest_tidak_bisa_akses_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/');
    }

    public function test_guest_tidak_bisa_akses_kasir_dashboard(): void
    {
        $response = $this->get('/kasir/dashboard');
        $response->assertRedirect('/');
    }

    public function test_guest_tidak_bisa_akses_kelola_pengguna(): void
    {
        $response = $this->get('/users');
        $response->assertRedirect('/');
    }

    public function test_guest_tidak_bisa_akses_produk(): void
    {
        $response = $this->get('/admin/produk');
        $response->assertRedirect('/');
    }

    public function test_guest_tidak_bisa_akses_log_stok(): void
    {
        $response = $this->get('/log-stok');
        $response->assertRedirect('/');
    }

    // ========================================================
    // SUPERADMIN ACCESS
    // ========================================================

    public function test_superadmin_bisa_akses_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_superadmin_bisa_akses_riwayat_transaksi(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $response = $this->actingAs($user)->get('/transactions');
        $response->assertStatus(200);
    }

    public function test_superadmin_bisa_akses_kelola_pengguna(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $response = $this->actingAs($user)->get('/users');
        $response->assertStatus(200);
    }

    public function test_superadmin_bisa_akses_log_stok(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $response = $this->actingAs($user)->get('/log-stok');
        $response->assertStatus(200);
    }

    public function test_superadmin_tidak_bisa_akses_admin_produk(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $response = $this->actingAs($user)->get('/admin/produk');
        $response->assertRedirect('/');
    }

    public function test_superadmin_tidak_bisa_akses_kasir_transaksi(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $response = $this->actingAs($user)->get('/kasir/transaksi');
        $response->assertRedirect('/');
    }

    // ========================================================
    // ADMIN ACCESS
    // ========================================================

    public function test_admin_bisa_akses_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_bisa_akses_produk(): void
    {
        $user = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($user)->get('/admin/produk');
        $response->assertStatus(200);
    }

    public function test_admin_bisa_akses_mitra(): void
    {
        $user = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($user)->get('/admin/mitra');
        $response->assertStatus(200);
    }

    public function test_admin_bisa_akses_riwayat_transaksi(): void
    {
        $user = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($user)->get('/admin/transactions');
        $response->assertStatus(200);
    }

    public function test_admin_bisa_akses_log_stok(): void
    {
        $user = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($user)->get('/log-stok');
        $response->assertStatus(200);
    }

    public function test_admin_tidak_bisa_akses_superadmin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect('/');
    }

    public function test_admin_tidak_bisa_akses_kelola_pengguna(): void
    {
        $user = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($user)->get('/users');
        $response->assertRedirect('/');
    }

    public function test_admin_tidak_bisa_akses_kasir_transaksi(): void
    {
        $user = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($user)->get('/kasir/transaksi');
        $response->assertRedirect('/');
    }

    // ========================================================
    // KASIR ACCESS
    // ========================================================

    public function test_kasir_bisa_akses_kasir_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'Kasir']);

        $response = $this->actingAs($user)->get('/kasir/dashboard');
        $response->assertStatus(200);
    }

    public function test_kasir_bisa_akses_input_transaksi(): void
    {
        $user = User::factory()->create(['role' => 'Kasir']);

        $response = $this->actingAs($user)->get('/kasir/transaksi');
        $response->assertStatus(200);
    }

    public function test_kasir_bisa_akses_riwayat(): void
    {
        $user = User::factory()->create(['role' => 'Kasir']);

        $response = $this->actingAs($user)->get('/kasir/riwayat');
        $response->assertStatus(200);
    }

    public function test_kasir_bisa_akses_tagihan(): void
    {
        $user = User::factory()->create(['role' => 'Kasir']);

        $response = $this->actingAs($user)->get('/kasir/tagihan');
        $response->assertStatus(200);
    }

    public function test_kasir_bisa_akses_log_stok(): void
    {
        $user = User::factory()->create(['role' => 'Kasir']);

        $response = $this->actingAs($user)->get('/log-stok');
        $response->assertStatus(200);
    }

    public function test_kasir_tidak_bisa_akses_superadmin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'Kasir']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect('/');
    }

    public function test_kasir_tidak_bisa_akses_admin_produk(): void
    {
        $user = User::factory()->create(['role' => 'Kasir']);

        $response = $this->actingAs($user)->get('/admin/produk');
        $response->assertRedirect('/');
    }

    public function test_kasir_tidak_bisa_akses_kelola_pengguna(): void
    {
        $user = User::factory()->create(['role' => 'Kasir']);

        $response = $this->actingAs($user)->get('/users');
        $response->assertRedirect('/');
    }
}
