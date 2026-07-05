<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'Admin']);
    }

    /**
     * TC-NTF-001: Melihat Daftar Notifikasi (Positive)
     */
    public function test_melihat_daftar_notifikasi(): void
    {
        Notification::create([
            'user_id' => $this->admin->id,
            'title' => 'Stok Rendah',
            'message' => 'Ayam Broiler mendekati batas minimum.',
            'type' => 'stok_rendah',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->admin)->get('/notifications');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'notifications',
            'unread_count'
        ]);
        $response->assertJsonFragment(['title' => 'Stok Rendah']);
    }

    /**
     * TC-NTF-002: Melihat Detail Notifikasi (Positive)
     */
    public function test_melihat_detail_notifikasi(): void
    {
        $notif = Notification::create([
            'user_id' => $this->admin->id,
            'title' => 'Bukti Bayar Diupload',
            'message' => 'Mitra A telah mengupload bukti bayar.',
            'type' => 'bukti_pembayaran',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->admin)->postJson("/notifications/{$notif->id}/read");

        $response->assertStatus(200);
        $this->assertTrue((bool)$notif->refresh()->is_read);
    }

    /**
     * TC-NTF-003: Tandai Semua Sudah Dibaca (Positive)
     */
    public function test_tandai_semua_sudah_dibaca(): void
    {
        Notification::create([
            'user_id' => $this->admin->id,
            'title' => 'Notif 1',
            'message' => 'Pesan 1',
            'type' => 'info',
            'is_read' => false,
        ]);
        Notification::create([
            'user_id' => $this->admin->id,
            'title' => 'Notif 2',
            'message' => 'Pesan 2',
            'type' => 'info',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/notifications/read-all');

        $response->assertStatus(200);
        $response->assertJson(['unread_count' => 0]);
        $this->assertEquals(0, Notification::where('user_id', $this->admin->id)->where('is_read', false)->count());
    }

    /**
     * TC-NTF-004: Tidak Ada Notifikasi (Negative)
     */
    public function test_tidak_ada_notifikasi(): void
    {
        $response = $this->actingAs($this->admin)->get('/notifications');

        $response->assertStatus(200);
        $response->assertJson([
            'notifications' => [],
            'unread_count' => 0
        ]);
    }

    /**
     * TC-NTF-005: Tandai Semua Sudah Dibaca Saat Semua Notifikasi Sudah Dibaca (Negative)
     */
    public function test_tandai_semua_sudah_dibaca_saat_semua_notifikasi_sudah_dibaca(): void
    {
        Notification::create([
            'user_id' => $this->admin->id,
            'title' => 'Notif Lama',
            'message' => 'Sudah dibaca',
            'type' => 'info',
            'is_read' => true,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/notifications/read-all');

        $response->assertStatus(200);
        $response->assertJson(['unread_count' => 0]);
    }
}
