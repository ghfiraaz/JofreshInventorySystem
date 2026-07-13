<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Login Valid
     * Admin/User memasukkan email dan password yang terdaftar kemudian diarahkan ke dashboard sesuai role.
     */
    public function test_login_valid(): void
    {
        $password = 'password123';
        
        // 1. Test Superadmin
        $superadmin = User::factory()->create([
            'email' => 'owner@jofresh.com',
            'password' => bcrypt($password),
            'role' => 'Superadmin',
        ]);
        $response = $this->post('/login', [
            'email' => $superadmin->email,
            'password' => $password,
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($superadmin);

        $this->post('/logout');

        // 2. Test Admin
        $admin = User::factory()->create([
            'email' => 'admin@jofresh.com',
            'password' => bcrypt($password),
            'role' => 'Admin',
        ]);
        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => $password,
        ]);
        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);

        $this->post('/logout');

        // 3. Test Kasir
        $kasir = User::factory()->create([
            'email' => 'kasir@jofresh.com',
            'password' => bcrypt($password),
            'role' => 'Kasir',
        ]);
        $response = $this->post('/login', [
            'email' => $kasir->email,
            'password' => $password,
        ]);
        $response->assertRedirect('/kasir');
        $this->assertAuthenticatedAs($kasir);
    }

    /**
     * Password Salah
     * Memasukkan email terdaftar namun password salah.
     */
    public function test_password_salah(): void
    {
        $user = User::factory()->create([
            'email' => 'owner@jofresh.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->from('/')
            ->post('/login', [
                'email' => $user->email,
                'password' => 'wrongpassword',
            ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['password' => 'Kata sandi salah']);
        $this->assertGuest();
    }

    /**
     * Akun Tidak Terdaftar
     * Mencoba login menggunakan email yang tidak terdaftar pada sistem.
     */
    public function test_akun_tidak_terdaftar(): void
    {
        $response = $this->from('/')
            ->post('/login', [
                'email' => 'unregistered@jofresh.com',
                'password' => 'password123',
            ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['email' => 'Akun tidak terdaftar']);
        $this->assertGuest();
    }

    /**
     * Format Email Tidak Valid
     * Memasukkan email dengan format yang tidak valid (tanpa domain / @).
     */
    public function test_format_email_tidak_valid(): void
    {
        $response = $this->from('/')
            ->post('/login', [
                'email' => 'ownerjofresh.com',
                'password' => 'password123',
            ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Password 7 Karakter
     * Menggunakan password sepanjang 7 karakter yang salah untuk akun terdaftar.
     */
    public function test_password_7_karakter(): void
    {
        $user = User::factory()->create([
            'email' => 'owner@jofresh.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->from('/')
            ->post('/login', [
                'email' => $user->email,
                'password' => '1234567',
            ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['password' => 'Kata sandi salah']);
        $this->assertGuest();
    }

    /**
     *  Password 8 Karakter
     * Menggunakan password sepanjang 8 karakter yang tepat untuk masuk ke sistem.
     */
    public function test_password_8_karakter(): void
    {
        $password = '12345678';
        $user = User::factory()->create([
            'email' => 'owner@jofresh.com',
            'password' => bcrypt($password),
            'role' => 'Superadmin',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Password 9 Karakter
     * Menggunakan password sepanjang 9 karakter yang tepat untuk masuk ke sistem.
     */
    public function test_password_9_karakter(): void
    {
        $password = '123456789';
        $user = User::factory()->create([
            'email' => 'owner@jofresh.com',
            'password' => bcrypt($password),
            'role' => 'Superadmin',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
