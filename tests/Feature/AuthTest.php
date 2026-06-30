<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ========================================================
    // LOGIN PAGE
    // ========================================================

    public function test_halaman_login_dapat_diakses(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Dashboard');
    }

    public function test_user_sudah_login_diarahkan_dari_halaman_login(): void
    {
        $user = User::factory()->create(['role' => 'Superadmin']);

        $response = $this->actingAs($user)->get('/');
        $response->assertRedirect('/dashboard');
    }

    // ========================================================
    // LOGIN BERHASIL
    // ========================================================

    public function test_superadmin_login_berhasil_redirect_ke_dashboard(): void
    {
        $user = User::factory()->create([
            'email'    => 'owner@jofresh.com',
            'password' => bcrypt('password123'),
            'role'     => 'Superadmin',
        ]);

        $response = $this->post('/login', [
            'email'    => 'owner@jofresh.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_login_berhasil_redirect_ke_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'email'    => 'admin@jofresh.com',
            'password' => bcrypt('password123'),
            'role'     => 'Admin',
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin@jofresh.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_kasir_login_berhasil_redirect_ke_kasir(): void
    {
        $user = User::factory()->create([
            'email'    => 'kasir@jofresh.com',
            'password' => bcrypt('password123'),
            'role'     => 'Kasir',
        ]);

        $response = $this->post('/login', [
            'email'    => 'kasir@jofresh.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/kasir');
        $this->assertAuthenticatedAs($user);
    }

    // ========================================================
    // LOGIN GAGAL
    // ========================================================

    public function test_login_gagal_email_tidak_ditemukan(): void
    {
        $response = $this->post('/login', [
            'email'    => 'tidakada@jofresh.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_gagal_password_salah(): void
    {
        User::factory()->create([
            'email'    => 'owner@jofresh.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'owner@jofresh.com',
            'password' => 'salahpassword',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_login_gagal_email_kosong(): void
    {
        $response = $this->post('/login', [
            'email'    => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_gagal_password_kosong(): void
    {
        $response = $this->post('/login', [
            'email'    => 'owner@jofresh.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_login_gagal_format_email_tidak_valid(): void
    {
        $response = $this->post('/login', [
            'email'    => 'bukan-email',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ========================================================
    // LOGOUT
    // ========================================================

    public function test_user_dapat_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_logout_tanpa_login_tetap_redirect(): void
    {
        $response = $this->post('/logout');
        $response->assertRedirect('/');
    }
}
