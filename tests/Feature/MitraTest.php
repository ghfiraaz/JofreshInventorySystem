<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MitraTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'Admin']);
    }

    // ========================================================
    // HALAMAN MITRA
    // ========================================================

    public function test_halaman_mitra_dapat_diakses_admin(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/mitra');
        $response->assertStatus(200);
    }

    public function test_halaman_mitra_menampilkan_daftar_mitra(): void
    {
        Mitra::create([
            'nama' => 'Warung Ayam Bu Sari', 'alamat' => 'Jl. Merdeka No.1',
            'status' => 'Aktif', 'tanggal_jatuh_tempo' => 15,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/mitra');
        $response->assertStatus(200);
        $response->assertSee('Warung Ayam Bu Sari');
    }

    // ========================================================
    // TAMBAH MITRA — BERHASIL
    // ========================================================

    public function test_admin_bisa_tambah_mitra_baru(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'                => 'Mitra Baru',
            'kontak'              => '08123456789',
            'email'               => 'mitra@gmail.com',
            'alamat'              => 'Jl. Kenari No.5',
            'tanggal_jatuh_tempo' => 25,
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Mitra berhasil ditambahkan']);
        $this->assertDatabaseHas('mitra', [
            'nama'   => 'Mitra Baru',
            'status' => 'Aktif',
        ]);
    }

    public function test_tambah_mitra_tanpa_kontak_dan_email_berhasil(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'   => 'Mitra Minimal',
            'alamat' => 'Jl. Simpang No.1',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('mitra', ['nama' => 'Mitra Minimal']);
    }

    public function test_tambah_mitra_otomatis_dapat_payment_token(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'   => 'Mitra Token',
            'alamat' => 'Jl. Token No.1',
        ]);

        $response->assertStatus(201);
        $mitra = Mitra::where('nama', 'Mitra Token')->first();
        $this->assertNotNull($mitra->payment_token);
    }

    // ========================================================
    // TAMBAH MITRA — GAGAL VALIDASI
    // ========================================================

    public function test_tambah_mitra_gagal_nama_kosong(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'   => '',
            'alamat' => 'Jl. Test',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('nama');
    }

    public function test_tambah_mitra_gagal_alamat_kosong(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'   => 'Mitra Test',
            'alamat' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('alamat');
    }

    public function test_tambah_mitra_gagal_email_bukan_gmail(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'   => 'Mitra Test',
            'alamat' => 'Jl. Test',
            'email'  => 'mitra@yahoo.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_tambah_mitra_gagal_kontak_bukan_angka(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'   => 'Mitra Test',
            'alamat' => 'Jl. Test',
            'kontak' => 'abcdefghij',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('kontak');
    }

    public function test_tambah_mitra_gagal_kontak_terlalu_pendek(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'   => 'Mitra Test',
            'alamat' => 'Jl. Test',
            'kontak' => '0812345',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('kontak');
    }

    public function test_tambah_mitra_gagal_kontak_sudah_terdaftar(): void
    {
        Mitra::create([
            'nama' => 'Mitra Existing', 'alamat' => 'Jl. Lama',
            'kontak' => '08123456789', 'status' => 'Aktif', 'tanggal_jatuh_tempo' => 1,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'   => 'Mitra Baru',
            'alamat' => 'Jl. Baru',
            'kontak' => '08123456789',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('kontak');
    }

    public function test_tambah_mitra_gagal_email_sudah_terdaftar(): void
    {
        Mitra::create([
            'nama' => 'Mitra Existing', 'alamat' => 'Jl. Lama',
            'email' => 'existing@gmail.com', 'status' => 'Aktif', 'tanggal_jatuh_tempo' => 1,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'   => 'Mitra Baru',
            'alamat' => 'Jl. Baru',
            'email'  => 'existing@gmail.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    // ========================================================
    // UPDATE MITRA (TOMBOL EDIT DI LIST MITRA)
    // ========================================================

    public function test_admin_bisa_update_mitra_dari_tombol_edit_di_list(): void
    {
        $mitra = Mitra::create([
            'nama' => 'Mitra Lama', 'alamat' => 'Jl. Lama',
            'status' => 'Aktif', 'tanggal_jatuh_tempo' => 10,
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/mitra/{$mitra->id}", [
            'nama'                => 'Mitra Updated',
            'alamat'              => 'Jl. Baru No.10',
            'tanggal_jatuh_tempo' => 20,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Mitra berhasil diperbarui']);
        $this->assertDatabaseHas('mitra', [
            'id'   => $mitra->id,
            'nama' => 'Mitra Updated',
        ]);
    }

    // ========================================================
    // HAPUS MITRA (TOMBOL HAPUS DI LIST MITRA)
    // ========================================================

    public function test_admin_bisa_hapus_mitra_dari_tombol_hapus_di_list(): void
    {
        $mitra = Mitra::create([
            'nama' => 'Mitra Hapus', 'alamat' => 'Jl. Hapus',
            'status' => 'Aktif', 'tanggal_jatuh_tempo' => 1,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson("/admin/mitra/{$mitra->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Mitra berhasil dihapus']);
        $this->assertDatabaseMissing('mitra', ['id' => $mitra->id]);
    }

    public function test_hapus_mitra_yang_tidak_ada_gagal(): void
    {
        $response = $this->actingAs($this->admin)->deleteJson('/admin/mitra/99999');
        $response->assertStatus(404);
    }
}
