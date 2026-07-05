<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class CleanAllExceptUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->warn('⚠️  PERHATIAN: Semua data transaksi, log stok, notifikasi, mitra, dan produk akan dihapus!');
        $this->command->info('Memulai pembersihan data...');

        // Disable foreign key checks untuk TRUNCATE
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // List of tables to truncate
        $tables = [
            'reminder_histories',
            'transaksi_items',
            'transaksi',
            'log_stok',
            'notifications',
            'mitra',
            'produk',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("✓ Tabel {$table} berhasil dikosongkan");
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        // Hapus file bukti pembayaran yang sudah diupload
        $buktiPath = 'bukti-pembayaran';
        if (Storage::disk('public')->exists($buktiPath)) {
            $files = Storage::disk('public')->files($buktiPath);
            foreach ($files as $file) {
                Storage::disk('public')->delete($file);
            }
            $this->command->info('✓ File bukti pembayaran dihapus (' . count($files) . ' file)');
        }

        // Hapus file invoice yang sudah di-generate
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
        $this->command->info('🎉 Pembersihan selesai! Semua data transaksi, log stok, notifikasi, mitra, dan produk telah dihapus.');
        $this->command->info('   - Hanya daftar pengguna (users) yang tetap dipertahankan.');
    }
}
