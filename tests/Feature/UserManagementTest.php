<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => 'Superadmin']);
    }

    // ========================================================
    // HALAMAN KELOLA PENGGUNA
    // ========================================================

    public function test_halaman_kelola_pengguna_menampilkan_daftar_user(): void
    {
        User::factory()->create(['name' => 'Budi Kasir', 'role' => 'Kasir']);
        User::factory()->create(['name' => 'Sari Admin', 'role' => 'Admin']);

        $response = $this->actingAs($this->superadmin)->get('/users');

        $response->assertStatus(200);
        $response->assertSee('Budi Kasir');
        $response->assertSee('Sari Admin');
    }

    // ========================================================
    // TAMBAH USER — BERHASIL
    // ========================================================

    public function test_superadmin_bisa_tambah_user_baru(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => 'Kasir Baru',
            'email'    => 'kasirbaru@jofresh.com',
            'password' => 'password1',
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'User created successfully']);
        $this->assertDatabaseHas('users', [
            'email' => 'kasirbaru@jofresh.com',
            'role'  => 'Kasir',
        ]);
    }

    // ========================================================
    // TAMBAH USER — GAGAL VALIDASI
    // ========================================================

    public function test_tambah_user_gagal_email_bukan_domain_jofresh(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => 'Test User',
            'email'    => 'test@gmail.com',
            'password' => 'password1',
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_tambah_user_gagal_email_sudah_digunakan(): void
    {
        User::factory()->create(['email' => 'existing@jofresh.com']);

        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => 'Test User',
            'email'    => 'existing@jofresh.com',
            'password' => 'password1',
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_tambah_user_gagal_password_kurang_dari_8_karakter(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => 'Test User',
            'email'    => 'test@jofresh.com',
            'password' => 'pas1',
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_tambah_user_gagal_password_tanpa_angka(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => 'Test User',
            'email'    => 'test@jofresh.com',
            'password' => 'passwordtanpaangka',
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_tambah_user_gagal_nama_kosong(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => '',
            'email'    => 'test@jofresh.com',
            'password' => 'password1',
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_tambah_user_gagal_role_tidak_valid(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => 'Test User',
            'email'    => 'test@jofresh.com',
            'password' => 'password1',
            'role'     => 'RoleTidakAda',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('role');
    }

    // ========================================================
    // UPDATE USER
    // ========================================================

    public function test_superadmin_bisa_update_user(): void
    {
        $user = User::factory()->create([
            'name'  => 'Old Name',
            'email' => 'old@jofresh.com',
            'role'  => 'Kasir',
        ]);

        $response = $this->actingAs($this->superadmin)->putJson("/users/{$user->id}", [
            'name'  => 'New Name',
            'email' => 'new@jofresh.com',
            'role'  => 'Admin',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'User updated successfully']);
        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => 'New Name',
            'email' => 'new@jofresh.com',
            'role'  => 'Admin',
        ]);
    }

    public function test_update_user_bisa_tanpa_ganti_password(): void
    {
        $user = User::factory()->create([
            'email' => 'kasir@jofresh.com',
            'role'  => 'Kasir',
        ]);

        $response = $this->actingAs($this->superadmin)->putJson("/users/{$user->id}", [
            'name'  => 'Updated Name',
            'email' => 'kasir@jofresh.com',
            'role'  => 'Kasir',
        ]);

        $response->assertStatus(200);
    }

    // ========================================================
    // HAPUS USER
    // ========================================================

    public function test_superadmin_bisa_hapus_user(): void
    {
        $user = User::factory()->create(['email' => 'hapus@jofresh.com']);

        $response = $this->actingAs($this->superadmin)->deleteJson("/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'User deleted successfully']);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_hapus_user_yang_tidak_ada_gagal(): void
    {
        $response = $this->actingAs($this->superadmin)->deleteJson('/users/99999');

        $response->assertStatus(404);
    }
}
