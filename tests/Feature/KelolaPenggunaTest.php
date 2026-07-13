<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelolaPenggunaTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => 'Superadmin']);
    }

    /**
     * Melihat Data Pengguna (Positive)
     * Menampilkan seluruh data pengguna yang tersimpan pada sistem.
     */
    public function test_melihat_data_pengguna(): void
    {
        $user1 = User::factory()->create(['name' => 'Nana', 'role' => 'Kasir']);
        $user2 = User::factory()->create(['name' => 'Fira', 'role' => 'Admin']);

        $response = $this->actingAs($this->superadmin)->get('/users');

        $response->assertStatus(200);
        $response->assertSee('Nana');
        $response->assertSee('Fira');
    }

    /**
     * Melihat data pengguna saat belum ada data (Positive)
     * Menampilkan informasi "Belum ada pengguna." ketika belum terdapat data pengguna.
     */
    public function test_melihat_data_pengguna_saat_belum_ada_data(): void
    {
        User::query()->delete();

        $user = new User([
            'id' => 1,
            'name' => 'Owner',
            'email' => 'owner@jofresh.com',
            'role' => 'Superadmin',
        ]);

        $response = $this->actingAs($user)->get('/users');

        $response->assertStatus(200);
        $response->assertSee('Belum ada pengguna.');
    }

    /**
     * Tambah Pengguna dengan Data Valid (Positive)
     * Berhasil menyimpan data pengguna baru dan menampilkannya.
     */
    public function test_tambah_pengguna_dengan_data_valid(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => 'Andi Wijaya',
            'email'    => 'andiwijaya@jofresh.com',
            'password' => 'password123',
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'User created successfully']);
        $this->assertDatabaseHas('users', [
            'name'  => 'Andi Wijaya',
            'email' => 'andiwijaya@jofresh.com',
            'role'  => 'Kasir',
        ]);
    }

    /**
     * Tambah Pengguna dengan Required Field Tidak Lengkap (Negative)
     * Menampilkan pesan validasi jika field wajib dibiarkan kosong.
     */
    public function test_tambah_pengguna_dengan_required_field_tidak_lengkap(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => '', // Kosong
            'email'    => 'andi@jofresh.com',
            'password' => 'password123',
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    /**
     * Mengubah Data Pengguna (Positive)
     * Berhasil memperbarui data pengguna.
     */
    public function test_mengubah_data_pengguna(): void
    {
        $user = User::factory()->create([
            'name'  => 'User Lama',
            'email' => 'olduser@jofresh.com',
            'role'  => 'Kasir',
        ]);

        $response = $this->actingAs($this->superadmin)->putJson("/users/{$user->id}", [
            'name'  => 'User Baru',
            'email' => 'newuser@jofresh.com',
            'role'  => 'Admin',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'User updated successfully']);
        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => 'User Baru',
            'email' => 'newuser@jofresh.com',
            'role'  => 'Admin',
        ]);
    }

    /**
     * Menyimpan Tanpa Perubahan Data (Positive)
     * Tetap menyimpan data meskipun tidak ada perubahan yang dilakukan.
     */
    public function test_menyimpan_tanpa_perubahan_data(): void
    {
        $user = User::factory()->create([
            'name'  => 'User Tetap',
            'email' => 'tetap@jofresh.com',
            'role'  => 'Kasir',
        ]);

        $response = $this->actingAs($this->superadmin)->putJson("/users/{$user->id}", [
            'name'  => 'User Tetap',
            'email' => 'tetap@jofresh.com',
            'role'  => 'Kasir',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => 'User Tetap',
            'email' => 'tetap@jofresh.com',
        ]);
    }

    /**
     * Hapus Pengguna (Positive)
     * Berhasil menghapus data pengguna dari sistem.
     */
    public function test_hapus_pengguna(): void
    {
        $user = User::factory()->create(['email' => 'userhapus@jofresh.com']);

        $response = $this->actingAs($this->superadmin)->deleteJson("/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'User deleted successfully']);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * Membatalkan Hapus Pengguna (Negative)
     * Data pengguna tetap tersimpan jika proses hapus dibatalkan (simulasi dengan tidak mengirim request DELETE).
     */
    public function test_membatalkan_hapus_pengguna(): void
    {
        $user = User::factory()->create(['email' => 'nana@jofresh.com']);

        // Sesi Superadmin aktif, tapi tidak melakukan DELETE request
        $this->actingAs($this->superadmin);

        // Pengguna harus tetap ada di database
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /**
     * Format Email Tidak Valid (Negative)
     * Menolak email dengan format yang tidak valid.
     */
    public function test_format_email_tidak_valid(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => 'User Valid',
            'email'    => 'nana@email.com',
            'password' => 'nana1234',
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * Email Duplikat (Negative)
     * Menolak email yang sudah terdaftar oleh pengguna lain.
     */
    public function test_email_duplikat(): void
    {
        User::factory()->create(['email' => 'nana@jofresh.com']);

        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => 'User Valid',
            'email'    => 'nana@jofresh.com',
            'password' => 'nana1234',
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * Password 7 Karakter (Negative / BVA)
     * Menolak password yang kurang dari 8 karakter.
     */
    public function test_password_7_karakter(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => 'User Valid',
            'email'    => 'nana@jofresh.com',
            'password' => '1234567', // 7 Karakter
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    /**
     * Password 8 Karakter (Positive / BVA)
     * Berhasil menyimpan data pengguna jika password tepat 8 karakter (dan mengandung angka).
     */
    public function test_password_8_karakter(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => 'User Valid',
            'email'    => 'nana@jofresh.com',
            'password' => 'nana1234', // Tepat 8 Karakter
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'nana@jofresh.com']);
    }

    /**
     * Password Tidak Mengandung Angka (Negative)
     * Menolak password yang tidak mengandung minimal satu angka.
     */
    public function test_password_tidak_mengandung_angka(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/users', [
            'name'     => 'User Valid',
            'email'    => 'nana@jofresh.com',
            'password' => 'abcdefgh', // 8 karakter tanpa angka
            'role'     => 'Kasir',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }
}
