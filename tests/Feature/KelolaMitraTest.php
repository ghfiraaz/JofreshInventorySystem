<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelolaMitraTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'Admin']);
    }

    /**
     * TC-KMT-001: Tambah Mitra (Positive)
     * Berhasil menyimpan data mitra baru jika seluruh data valid.
     */
    public function test_tc_kmt_001_tambah_mitra(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'                => 'Mitra Sukses',
            'kontak'              => '081234567890',
            'email'               => 'sukses@gmail.com',
            'alamat'              => 'Jl. Sukses No. 10',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Mitra berhasil ditambahkan']);
        $this->assertDatabaseHas('mitra', [
            'nama'   => 'Mitra Sukses',
            'kontak' => '081234567890',
            'email'  => 'sukses@gmail.com',
            'alamat' => 'Jl. Sukses No. 10',
        ]);
    }

    /**
     * TC-KMT-002: Tambah Mitra dengan Data Tidak Lengkap (Negative)
     * Menampilkan pesan validasi jika field wajib (seperti nama) dikosongkan.
     */
    public function test_tc_kmt_002_tambah_mitra_dengan_data_tidak_lengkap(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'                => '', // Kosong
            'kontak'              => '081234567890',
            'email'               => 'sukses@gmail.com',
            'alamat'              => 'Jl. Sukses No. 10',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('nama');
    }

    /**
     * TC-KMT-003: Melihat Data Mitra (Positive)
     * Menampilkan seluruh data mitra yang tersimpan pada sistem.
     */
    public function test_tc_kmt_003_melihat_data_mitra(): void
    {
        Mitra::create([
            'nama'                => 'Mitra A',
            'email'               => 'mitraA@gmail.com',
            'kontak'              => '081234567891',
            'alamat'              => 'Jl. Mitra A',
            'tanggal_jatuh_tempo' => 10,
            'status'              => 'Aktif',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/mitra');

        $response->assertStatus(200);
        $response->assertSee('Mitra A');
    }

    /**
     * TC-KMT-004: Melihat Data Mitra Saat Belum Ada Data (Positive)
     * Menampilkan pesan "Belum ada mitra terdaftar." jika database kosong.
     */
    public function test_tc_kmt_004_melihat_data_mitra_saat_belum_ada_data(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/mitra');

        $response->assertStatus(200);
        $response->assertSee('Belum ada mitra terdaftar.');
    }

    /**
     * TC-KMT-005: Mengubah Data Mitra (Positive)
     * Berhasil memperbarui data mitra sesuai perubahan yang dilakukan.
     */
    public function test_tc_kmt_005_mengubah_data_mitra(): void
    {
        $mitra = Mitra::create([
            'nama'                => 'Mitra Lama',
            'email'               => 'lama@gmail.com',
            'kontak'              => '081234567892',
            'alamat'              => 'Jl. Lama No. 5',
            'tanggal_jatuh_tempo' => 10,
            'status'              => 'Aktif',
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/mitra/{$mitra->id}", [
            'nama'                => 'Mitra Baru',
            'email'               => 'baru@gmail.com',
            'kontak'              => '081234567899',
            'alamat'              => 'Jl. Baru No. 8',
            'tanggal_jatuh_tempo' => 20,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Mitra berhasil diperbarui']);
        $this->assertDatabaseHas('mitra', [
            'id'    => $mitra->id,
            'nama'  => 'Mitra Baru',
            'email' => 'baru@gmail.com',
        ]);
    }

    /**
     * TC-KMT-006: Menyimpan Tanpa Perubahan Data (Positive)
     * Tetap menyimpan data meskipun tidak ada informasi yang diubah.
     */
    public function test_tc_kmt_006_menyimpan_tanpa_perubahan_data(): void
    {
        $mitra = Mitra::create([
            'nama'                => 'Mitra Tetap',
            'email'               => 'tetap@gmail.com',
            'kontak'              => '081234567893',
            'alamat'              => 'Jl. Tetap No. 5',
            'tanggal_jatuh_tempo' => 10,
            'status'              => 'Aktif',
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/mitra/{$mitra->id}", [
            'nama'                => 'Mitra Tetap',
            'email'               => 'tetap@gmail.com',
            'kontak'              => '081234567893',
            'alamat'              => 'Jl. Tetap No. 5',
            'tanggal_jatuh_tempo' => 10,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('mitra', [
            'id'   => $mitra->id,
            'nama' => 'Mitra Tetap',
        ]);
    }

    /**
     * TC-KMT-007: Hapus Mitra (Positive)
     * Berhasil menghapus data mitra dari sistem.
     */
    public function test_tc_kmt_007_hapus_mitra(): void
    {
        $mitra = Mitra::create([
            'nama'                => 'Mitra Hapus',
            'email'               => 'hapus@gmail.com',
            'kontak'              => '081234567894',
            'alamat'              => 'Jl. Hapus',
            'tanggal_jatuh_tempo' => 5,
            'status'              => 'Aktif',
        ]);

        $response = $this->actingAs($this->admin)->deleteJson("/admin/mitra/{$mitra->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Mitra berhasil dihapus']);
        $this->assertDatabaseMissing('mitra', ['id' => $mitra->id]);
    }

    /**
     * TC-KMT-008: Membatalkan Hapus Mitra (Negative)
     * Data mitra tetap tersimpan jika penghapusan dibatalkan (simulasi dengan tidak memanggil request DELETE).
     */
    public function test_tc_kmt_008_membatalkan_hapus_mitra(): void
    {
        $mitra = Mitra::create([
            'nama'                => 'Mitra Aman',
            'email'               => 'aman@gmail.com',
            'kontak'              => '081234567895',
            'alamat'              => 'Jl. Aman',
            'tanggal_jatuh_tempo' => 5,
            'status'              => 'Aktif',
        ]);

        $this->actingAs($this->admin);

        // Mitra harus tetap terdaftar di database
        $this->assertDatabaseHas('mitra', ['id' => $mitra->id]);
    }

    /**
     * TC-KMT-009: Nomor Telepon 9 Digit (Negative / BVA)
     * Menolak nomor telepon yang kurang dari 10 digit.
     */
    public function test_tc_kmt_009_nomor_telepon_9_digit(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'                => 'Mitra Kontak',
            'kontak'              => '081234567', // 9 Digit
            'email'               => 'kontak9@gmail.com',
            'alamat'              => 'Jl. Kontak',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('kontak');
    }

    /**
     * TC-KMT-010: Nomor Telepon 10 Digit (Positive / BVA)
     * Berhasil jika nomor telepon tepat 10 digit.
     */
    public function test_tc_kmt_010_nomor_telepon_10_digit(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'                => 'Mitra Kontak',
            'kontak'              => '0812345678', // 10 Digit
            'email'               => 'kontak10@gmail.com',
            'alamat'              => 'Jl. Kontak',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('mitra', ['email' => 'kontak10@gmail.com']);
    }

    /**
     * TC-KMT-011: Nomor Telepon 13 Digit (Max) (Positive / BVA)
     * Berhasil jika nomor telepon tepat 13 digit.
     */
    public function test_tc_kmt_011_nomor_telepon_13_digit(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'                => 'Mitra Kontak',
            'kontak'              => '0812345678901', // 13 Digit
            'email'               => 'kontak13@gmail.com',
            'alamat'              => 'Jl. Kontak',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('mitra', ['email' => 'kontak13@gmail.com']);
    }

    /**
     * TC-KMT-012: Nomor Telepon 14 Digit (Negative / BVA)
     * Menolak nomor telepon yang lebih dari 13 digit.
     */
    public function test_tc_kmt_012_nomor_telepon_14_digit(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'                => 'Mitra Kontak',
            'kontak'              => '08123456789012', // 14 Digit
            'email'               => 'kontak14@gmail.com',
            'alamat'              => 'Jl. Kontak',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('kontak');
    }

    /**
     * TC-KMT-013: Nomor Telepon Mengandung Huruf (Negative)
     * Menolak nomor telepon yang mengandung karakter huruf.
     */
    public function test_tc_kmt_013_nomor_telepon_mengandung_huruf(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'                => 'Mitra Kontak',
            'kontak'              => '0812abc4567',
            'email'               => 'kontakhuruf@gmail.com',
            'alamat'              => 'Jl. Kontak',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('kontak');
    }

    /**
     * TC-KMT-014: Nomor Telepon Mengandung Karakter Khusus (Negative)
     * Menolak nomor telepon yang mengandung karakter khusus.
     */
    public function test_tc_kmt_014_nomor_telepon_mengandung_karakter_khusus(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'                => 'Mitra Kontak',
            'kontak'              => '0812-345-678',
            'email'               => 'kontakkhusus@gmail.com',
            'alamat'              => 'Jl. Kontak',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('kontak');
    }

    /**
     * TC-KMT-015: Format Email Tidak Valid (Negative)
     * Menolak format email yang tidak valid.
     */
    public function test_tc_kmt_015_format_email_tidak_valid(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'                => 'Mitra Email',
            'kontak'              => '081234567890',
            'email'               => 'invalidemailformat',
            'alamat'              => 'Jl. Email',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * TC-KMT-016: Email Kosong (Negative)
     * Menolak jika email mitra dikosongkan.
     */
    public function test_tc_kmt_016_email_kosong(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'                => 'Mitra Email',
            'kontak'              => '081234567890',
            'email'               => '', // Kosong
            'alamat'              => 'Jl. Email',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * TC-KMT-017: Email Duplikat (Negative)
     * Menolak email yang sudah terdaftar oleh mitra lain.
     */
    public function test_tc_kmt_017_email_duplikat(): void
    {
        Mitra::create([
            'nama'                => 'Mitra Pertama',
            'email'               => 'duplikat@gmail.com',
            'alamat'              => 'Jl. Pertama',
            'tanggal_jatuh_tempo' => 15,
            'status'              => 'Aktif',
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/mitra', [
            'nama'                => 'Mitra Kedua',
            'kontak'              => '081234567899',
            'email'               => 'duplikat@gmail.com',
            'alamat'              => 'Jl. Kedua',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }
}
