<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelolaProdukTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'Admin']);
    }

    // =========================================================================
    // TAMBAH & LIHAT PRODUK
    // =========================================================================

    /**
     * TC-KPR-001: Melihat Data Produk (Positive)
     * Menampilkan seluruh data produk yang tersimpan pada sistem.
     */
    public function test_melihat_data_produk(): void
    {
        Produk::create([
            'nama' => 'Ayam Potong Segar',
            'kategori' => 'Unggas',
            'stok' => 10,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 45000,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/produk');
        $response->assertStatus(200);
        $response->assertSee('Ayam Potong Segar');
    }

    /**
     * Melihat Data Produk Saat Belum Ada Data
     * Menampilkan informasi "Belum ada produk. Klik "Tambah Produk" untuk memulai." jika data kosong.
     */
    public function test_melihat_data_produk_saat_belum_ada_data(): void
    {
        Produk::query()->delete();

        $response = $this->actingAs($this->admin)->get('/admin/produk');
        $response->assertStatus(200);
        $response->assertSee('Belum ada produk. Klik "Tambah Produk" untuk memulai.', false);
    }

    /**
     * Tambah Produk
     * Berhasil menyimpan data produk baru.
     */
    public function test_tambah_produk(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama' => 'Ayam Fillet',
            'harga' => 50000,
            'stok_minimal' => 10,
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Produk berhasil ditambahkan']);
        $this->assertDatabaseHas('produk', [
            'nama' => 'Ayam Fillet',
            'harga' => 50000,
            'stok_minimal' => 10,
        ]);
    }

    /**
     * Tambah Produk dengan Required Field Tidak Lengkap
     * Menolak jika salah satu field wajib dikosongkan.
     */
    public function test_tambah_produk_dengan_required_field_tidak_lengkap(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama' => '', // Kosong
            'harga' => 50000,
            'stok_minimal' => 10,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('nama');
    }

    /**
     * Harga Berupa Huruf
     * Menolak jika harga diisi karakter huruf.
     */
    public function test_harga_berupa_huruf(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama' => 'Ayam Broiler',
            'harga' => 'seratus', // Huruf
            'stok_minimal' => 10,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('harga');
    }

    /**
     * Harga Mengandung Karakter Khusus
     * Menolak jika harga mengandung karakter khusus.
     */
    public function test_harga_mengandung_karakter_khusus(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama' => 'Ayam Broiler',
            'harga' => '$30', // Karakter khusus
            'stok_minimal' => 10,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('harga');
    }

    /**
     * Harga Berupa Angka
     * Berhasil menyimpan produk jika harga berupa angka yang valid.
     */
    public function test_harga_berupa_angka(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama' => 'Ayam Kampung',
            'harga' => 65000, // Angka valid
            'stok_minimal' => 5,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('produk', ['nama' => 'Ayam Kampung', 'harga' => 65000]);
    }

    /**
     * minimal Stok = 0 (min-1)
     * Menolak batas stok minimal jika bernilai 0.
     */
    public function test_minimal_stok_nol(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama' => 'Ayam Broiler',
            'harga' => 45000,
            'stok_minimal' => 0, // Batas bawah - 1
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('stok_minimal');
    }

    /**
     * minimal Stok = 1 (Min)
     * Berhasil menyimpan produk jika batas stok minimal bernilai 1.
     */
    public function test_minimal_stok_satu(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama' => 'Ayam Broiler',
            'harga' => 45000,
            'stok_minimal' => 1, // Batas bawah
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('produk', ['nama' => 'Ayam Broiler', 'stok_minimal' => 1]);
    }

    // =========================================================================
    // EDIT PRODUK
    // =========================================================================

    /**
     * Edit Produk
     * Berhasil memperbarui data produk.
     */
    public function test_tc_kpr_010_edit_produk(): void
    {
        $produk = Produk::create([
            'nama' => 'Produk Lama',
            'kategori' => 'Unggas',
            'stok' => 10,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/produk/{$produk->id}", [
            'nama' => 'Produk Baru',
            'harga' => 35000,
            'stok_minimal' => 8,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('produk', [
            'id' => $produk->id,
            'nama' => 'Produk Baru',
            'harga' => 35000,
        ]);
    }

    /**
     * Edit Produk Tanpa Melakukan Perubahan
     * Tetap menyimpan data meskipun nilainya tidak ada yang diubah.
     */
    public function test_edit_produk_tanpa_melakukan_perubahan(): void
    {
        $produk = Produk::create([
            'nama' => 'Produk Tetap',
            'kategori' => 'Unggas',
            'stok' => 10,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/produk/{$produk->id}", [
            'nama' => 'Produk Tetap',
            'harga' => 30000,
            'stok_minimal' => 5,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('produk', [
            'id' => $produk->id,
            'nama' => 'Produk Tetap',
        ]);
    }

    /**
     * Edit Produk dengan Required Field Tidak Lengkap
     * Menolak pembaruan jika salah satu field wajib dikosongkan.
     */
    public function test_edit_produk_dengan_required_field_tidak_lengkap(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Segar',
            'kategori' => 'Unggas',
            'stok' => 10,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/produk/{$produk->id}", [
            'nama' => '', // Kosong
            'harga' => 35000,
            'stok_minimal' => 8,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('nama');
    }

    /**
     * Edit Harga Berupa Huruf
     * Menolak jika harga diubah menjadi karakter huruf.
     */
    public function test_edit_harga_berupa_huruf(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Segar',
            'kategori' => 'Unggas',
            'stok' => 10,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/produk/{$produk->id}", [
            'nama' => 'Ayam Segar',
            'harga' => 'tiga puluh ribu', // Huruf
            'stok_minimal' => 8,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('harga');
    }

    /**
     * Edit Harga Mengandung Karakter Khusus
     * Menolak jika harga diubah menjadi karakter khusus.
     */
    public function test_edit_harga_mengandung_karakter_khusus(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Segar',
            'kategori' => 'Unggas',
            'stok' => 10,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/produk/{$produk->id}", [
            'nama' => 'Ayam Segar',
            'harga' => '#35000', // Karakter khusus
            'stok_minimal' => 8,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('harga');
    }

    /**
     * Edit Harga Berupa Angka
     * Berhasil jika harga diganti dengan angka yang valid.
     */
    public function test_edit_harga_berupa_angka(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Segar',
            'kategori' => 'Unggas',
            'stok' => 10,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/produk/{$produk->id}", [
            'nama' => 'Ayam Segar',
            'harga' => 40000,
            'stok_minimal' => 8,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('produk', ['id' => $produk->id, 'harga' => 40000]);
    }

    /**
     * Edit minimal Stok = 0 (min-1)
     * Menolak jika batas minimal stok diubah menjadi 0.
     */
    public function test_edit_minimal_stok_nol(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Segar',
            'kategori' => 'Unggas',
            'stok' => 10,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/produk/{$produk->id}", [
            'nama' => 'Ayam Segar',
            'harga' => 30000,
            'stok_minimal' => 0, // Batas bawah - 1
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('stok_minimal');
    }

    /**
     * Edit minimal Stok = 1 (Min)
     * Berhasil memperbarui jika batas minimal stok diubah menjadi 1.
     */
    public function test_edit_minimal_stok_satu(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Segar',
            'kategori' => 'Unggas',
            'stok' => 10,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/produk/{$produk->id}", [
            'nama' => 'Ayam Segar',
            'harga' => 30000,
            'stok_minimal' => 1, // Batas bawah
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('produk', ['id' => $produk->id, 'stok_minimal' => 1]);
    }

    // =========================================================================
    // HAPUS PRODUK
    // =========================================================================

    /**
     * Hapus Produk
     * Berhasil menghapus data produk dari daftar produk.
     */
    public function test_tc_kpr_018_hapus_produk(): void
    {
        $produk = Produk::create([
            'nama' => 'Produk Hapus',
            'kategori' => 'Unggas',
            'stok' => 10,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson("/admin/produk/{$produk->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Produk berhasil dihapus']);
        $this->assertDatabaseMissing('produk', ['id' => $produk->id]);
    }

    /**
     * Membatalkan Hapus Produk
     * Data produk tetap disimpan jika proses hapus dibatalkan (tidak mengirim request DELETE).
     */
    public function test_membatalkan_hapus_produk(): void
    {
        $produk = Produk::create([
            'nama' => 'Produk Aman',
            'kategori' => 'Unggas',
            'stok' => 10,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $this->actingAs($this->admin);

        $this->assertDatabaseHas('produk', ['id' => $produk->id]);
    }

    // =========================================================================
    // TAMBAH STOK (STANDARD METHOD)
    // =========================================================================

    /**
     * Tambah Stok Produk
     * Berhasil menambahkan stok produk.
     */
    public function test_tc_kpr_020_tambah_stok_produk(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Negeri',
            'kategori' => 'Unggas',
            'stok' => 50,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->postJson("/admin/produk/{$produk->id}/stok", [
            'jumlah' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Stok berhasil ditambahkan']);
        $this->assertEquals(60, $produk->refresh()->stok);
    }

    /**
     * Tambah Stok dengan Jumlah Kosong
     * Menolak jika jumlah stok kosong.
     */
    public function test_tambah_stok_dengan_jumlah_kosong(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Negeri',
            'kategori' => 'Unggas',
            'stok' => 50,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->postJson("/admin/produk/{$produk->id}/stok", [
            'jumlah' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('jumlah');
    }

    /**
     * Stok Berupa Huruf
     * Menolak jika jumlah stok diisi karakter huruf.
     */
    public function test_stok_berupa_huruf(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Negeri',
            'kategori' => 'Unggas',
            'stok' => 50,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->postJson("/admin/produk/{$produk->id}/stok", [
            'jumlah' => 'sepuluh',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('jumlah');
    }

    /**
     * Stok Mengandung Karakter Khusus
     * Menolak jika jumlah stok mengandung karakter khusus.
     */
    public function test_stok_mengandung_karakter_khusus(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Negeri',
            'kategori' => 'Unggas',
            'stok' => 50,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->postJson("/admin/produk/{$produk->id}/stok", [
            'jumlah' => '10%',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('jumlah');
    }

    /**
     * Stok Mengandung Huruf dan Angka
     * Menolak jika jumlah stok mengandung huruf dan angka sekaligus.
     */
    public function test_stok_mengandung_huruf_dan_angka(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Negeri',
            'kategori' => 'Unggas',
            'stok' => 50,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->postJson("/admin/produk/{$produk->id}/stok", [
            'jumlah' => '10ekor',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('jumlah');
    }

    // =========================================================================
    // ADJUSTMENT STOK
    // =========================================================================

    /**
     * Menambah Stok Produk
     * Melalui fitur Adjust Stok, berhasil menambahkan jumlah stok.
     */
    public function test_tc_kpr_026_menambah_stok_produk_adjustment(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong',
            'kategori' => 'Unggas',
            'stok' => 20,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $produk->id,
            'tipe_adjustment' => 'Adjustment Masuk',
            'jumlah' => 15,
            'keterangan' => 'Penyesuaian restok barang masuk',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Adjustment stok berhasil dicatat.']);
        $this->assertEquals(35, $produk->refresh()->stok);
    }

    /**
     * Mengurangi Stok Produk
     * Melalui fitur Adjust Stok, berhasil mengurangi jumlah stok jika stok mencukupi.
     */
    public function test_mengurangi_stok_produk_adjustment(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong',
            'kategori' => 'Unggas',
            'stok' => 20,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $produk->id,
            'tipe_adjustment' => 'Adjustment Keluar',
            'jumlah' => 5,
            'keterangan' => 'Penyesuaian stok rusak',
        ]);

        $response->assertStatus(201);
        $this->assertEquals(15, $produk->refresh()->stok);
    }

    /**
     * Jumlah Adjust Stok Kosong
     * Menolak adjustment jika field jumlah stok dikosongkan.
     */
    public function test_jumlah_adjust_stok_kosong(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong',
            'kategori' => 'Unggas',
            'stok' => 20,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $produk->id,
            'tipe_adjustment' => 'Adjustment Masuk',
            'jumlah' => '',
            'keterangan' => 'Test keterangan',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('jumlah');
    }

    /**
     * Jumlah Adjust Stok Berupa Huruf
     * Menolak adjustment jika jumlah stok diisi karakter huruf.
     */
    public function test_jumlah_adjust_stok_berupa_huruf(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong',
            'kategori' => 'Unggas',
            'stok' => 20,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $produk->id,
            'tipe_adjustment' => 'Adjustment Masuk',
            'jumlah' => 'limabelas',
            'keterangan' => 'Test keterangan',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('jumlah');
    }

    /**
     * Mengurangi Stok Melebihi Stok Tersedia
     * Menolak jika jumlah pengurangan (Adjustment Keluar) melebihi stok saat ini.
     */
    public function test_mengurangi_stok_melebihi_stok_tersedia(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong',
            'kategori' => 'Unggas',
            'stok' => 10,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $produk->id,
            'tipe_adjustment' => 'Adjustment Keluar',
            'jumlah' => 15, // Melebihi stok 10
            'keterangan' => 'Test kurangi melebihi',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => "Stok Ayam Potong tidak cukup untuk dikurangi. Stok saat ini: 10."]);
    }

    /**
     * Jumlah Adjust Stok = 0 (min-1)
     * Menolak jika jumlah penyesuaian bernilai 0.
     */
    public function test_jumlah_adjust_stok_nol(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong',
            'kategori' => 'Unggas',
            'stok' => 20,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $produk->id,
            'tipe_adjustment' => 'Adjustment Masuk',
            'jumlah' => 0, // Batas bawah - 1
            'keterangan' => 'Test keterangan',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('jumlah');
    }

    /**
     * Jumlah Adjust Stok = 1 (Min)
     * Berhasil memproses jika jumlah penyesuaian bernilai 1.
     */
    public function test_jumlah_adjust_stok_satu(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong',
            'kategori' => 'Unggas',
            'stok' => 20,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 30000,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $produk->id,
            'tipe_adjustment' => 'Adjustment Masuk',
            'jumlah' => 1, // Batas bawah
            'keterangan' => 'Test keterangan',
        ]);

        $response->assertStatus(201);
        $this->assertEquals(21, $produk->refresh()->stok);
    }
}
