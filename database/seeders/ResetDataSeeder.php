<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResetDataSeeder extends Seeder
{
    /**
     * Reset seluruh data transaksi, mitra, pembayaran, dan reminder.
     * 
     * TIDAK menghapus: users, produk, sessions, cache, jobs, migrations.
     * Auto increment di-reset oleh TRUNCATE.
     */
    public function run(): void
    {
        $this->command->warn('⚠️  PERHATIAN: Semua data transaksi, mitra, dan pembayaran akan dihapus!');
        $this->command->info('Memulai proses reset data...');

        // Disable foreign key checks untuk TRUNCATE
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // 1. Truncate tabel reminder_histories (jika masih ada)
        if (\Illuminate\Support\Facades\Schema::hasTable('reminder_histories')) {
            DB::table('reminder_histories')->truncate();
            $this->command->info('✓ Tabel reminder_histories di-truncate');
        }

        // 2. Truncate tabel transaksi_items
        DB::table('transaksi_items')->truncate();
        $this->command->info('✓ Tabel transaksi_items di-truncate');

        // 3. Truncate tabel transaksi
        DB::table('transaksi')->truncate();
        $this->command->info('✓ Tabel transaksi di-truncate');

        // 4. Truncate tabel mitra
        DB::table('mitra')->truncate();
        $this->command->info('✓ Tabel mitra di-truncate');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        // 5. Hapus file bukti pembayaran yang sudah diupload
        $buktiPath = 'bukti-pembayaran';
        if (Storage::disk('public')->exists($buktiPath)) {
            $files = Storage::disk('public')->files($buktiPath);
            foreach ($files as $file) {
                Storage::disk('public')->delete($file);
            }
            $this->command->info('✓ File bukti pembayaran dihapus (' . count($files) . ' file)');
        }

        // 6. Hapus file invoice yang sudah di-generate
        $invoicePath = storage_path('app/invoices');
        if (is_dir($invoicePath)) {
            $invoiceFiles = glob($invoicePath . '/*');
            foreach ($invoiceFiles as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            $this->command->info('✓ File invoice dihapus (' . count($invoiceFiles) . ' file)');
        }

        $this->command->newLine();
        $this->command->info('🎉 Reset data selesai! Sistem siap digunakan untuk testing ulang.');
        $this->command->info('   - Akun login admin/owner/kasir tetap ada');
        $this->command->info('   - Data produk tetap ada');
        $this->command->info('   - Struktur tabel tetap utuh');
    }
}
