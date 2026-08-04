<?php

namespace Database\Seeders;

use App\Models\TaskInstance;
use App\Models\Subtask;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user
        $upin = User::where('email', 'upin@kpi.com')->first();
        $ipin = User::where('email', 'ipin@kpi.com')->first();

        if (!$upin || !$ipin) {
            $this->command->error('User Upin atau Ipin tidak ditemukan! Jalankan UserSeeder dulu.');
            return;
        }

        // ==================== TUGAS UPIN (5) ====================

        // 1. Rutin Harian
        $task = TaskInstance::create([
            'judul_spesifik' => 'Laporan Penjualan Harian',
            'deskripsi' => 'Membuat laporan penjualan harian dari data toko',
            'jenis' => 'rutin',
            'recurrence_type' => 'daily',
            'recurrence_day' => null,
            'recurrence_month' => null,
            'generate_offset_days' => 0,
            'bobot' => 3,
            'deadline' => now()->addDays(1)->toDateString(),
            'assignee_id' => $upin->id,
            'assignor_id' => $upin->id,
            'initiation_type' => 'proposal',
            'status' => 'proposed',
            'nilai' => 100,
        ]);
        Subtask::insert([
            ['task_instance_id' => $task->id, 'judul' => 'Kumpulkan data penjualan toko', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Buat grafik penjualan', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Kirim laporan ke atasan', 'is_done' => false],
        ]);

        // 2. Rutin Mingguan
        $task = TaskInstance::create([
            'judul_spesifik' => 'Rekap Stok Mingguan',
            'deskripsi' => 'Merekap stok barang di gudang setiap minggu',
            'jenis' => 'rutin',
            'recurrence_type' => 'weekly',
            'recurrence_day' => 5,
            'recurrence_month' => null,
            'generate_offset_days' => 2,
            'bobot' => 4,
            'deadline' => now()->addDays(5)->toDateString(),
            'assignee_id' => $upin->id,
            'assignor_id' => $upin->id,
            'initiation_type' => 'proposal',
            'status' => 'proposed',
            'nilai' => 100,
        ]);
        Subtask::insert([
            ['task_instance_id' => $task->id, 'judul' => 'Hitung stok di gudang', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Catat barang masuk/keluar', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Kirim laporan ke atasan', 'is_done' => false],
        ]);

        // 3. Rutin Bulanan
        $task = TaskInstance::create([
            'judul_spesifik' => 'Laporan Keuangan Bulanan',
            'deskripsi' => 'Membuat laporan keuangan akhir bulan',
            'jenis' => 'rutin',
            'recurrence_type' => 'monthly',
            'recurrence_day' => 1,
            'recurrence_month' => null,
            'generate_offset_days' => 5,
            'bobot' => 5,
            'deadline' => now()->addDays(7)->toDateString(),
            'assignee_id' => $upin->id,
            'assignor_id' => $upin->id,
            'initiation_type' => 'proposal',
            'status' => 'proposed',
            'nilai' => 100,
        ]);
        Subtask::insert([
            ['task_instance_id' => $task->id, 'judul' => 'Rekonsiliasi bank', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Buat laporan laba rugi', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Kirim ke direktur', 'is_done' => false],
        ]);

        // 4. Rutin Tahunan
        $task = TaskInstance::create([
            'judul_spesifik' => 'Perpanjang STNK Kendaraan Dinas',
            'deskripsi' => 'Mengurus perpanjangan STNK untuk kendaraan dinas',
            'jenis' => 'rutin',
            'recurrence_type' => 'yearly',
            'recurrence_day' => 1,
            'recurrence_month' => 7,
            'generate_offset_days' => 30,
            'bobot' => 6,
            'deadline' => '2026-07-01',
            'assignee_id' => $upin->id,
            'assignor_id' => $upin->id,
            'initiation_type' => 'proposal',
            'status' => 'proposed',
            'nilai' => 100,
        ]);
        Subtask::insert([
            ['task_instance_id' => $task->id, 'judul' => 'Siapkan berkas STNK', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Bayar pajak kendaraan', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Ambil STNK baru', 'is_done' => false],
        ]);

        // 5. Proyek
        $task = TaskInstance::create([
            'judul_spesifik' => 'Pengembangan Aplikasi KPI',
            'deskripsi' => 'Membangun aplikasi KPI untuk seluruh perusahaan',
            'jenis' => 'proyek',
            'recurrence_type' => null,
            'recurrence_day' => null,
            'recurrence_month' => null,
            'generate_offset_days' => 0,
            'bobot' => 9,
            'tanggal_mulai' => '2026-07-01',
            'deadline' => '2026-09-30',
            'assignee_id' => $upin->id,
            'assignor_id' => $upin->id,
            'initiation_type' => 'proposal',
            'status' => 'proposed',
            'nilai' => 100,
        ]);
        Subtask::insert([
            ['task_instance_id' => $task->id, 'judul' => 'Desain database', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Buat backend Laravel', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Buat frontend Next.js', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Testing dan deployment', 'is_done' => false],
        ]);

        // ==================== TUGAS IPIN (5) ====================

        // 1. Rutin Harian
        $task = TaskInstance::create([
            'judul_spesifik' => 'Laporan Pengeluaran Kas Harian',
            'deskripsi' => 'Mencatat semua pengeluaran kas harian',
            'jenis' => 'rutin',
            'recurrence_type' => 'daily',
            'recurrence_day' => null,
            'recurrence_month' => null,
            'generate_offset_days' => 0,
            'bobot' => 3,
            'deadline' => now()->addDays(1)->toDateString(),
            'assignee_id' => $ipin->id,
            'assignor_id' => $ipin->id,
            'initiation_type' => 'proposal',
            'status' => 'proposed',
            'nilai' => 100,
        ]);
        Subtask::insert([
            ['task_instance_id' => $task->id, 'judul' => 'Kumpulkan kwitansi', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Rekap pengeluaran per kategori', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Kirim laporan ke atasan', 'is_done' => false],
        ]);

        // 2. Rutin Mingguan
        $task = TaskInstance::create([
            'judul_spesifik' => 'Rekap Gaji Karyawan Mingguan',
            'deskripsi' => 'Merekap gaji karyawan untuk minggu ini',
            'jenis' => 'rutin',
            'recurrence_type' => 'weekly',
            'recurrence_day' => 6,
            'recurrence_month' => null,
            'generate_offset_days' => 2,
            'bobot' => 4,
            'deadline' => now()->addDays(6)->toDateString(),
            'assignee_id' => $ipin->id,
            'assignor_id' => $ipin->id,
            'initiation_type' => 'proposal',
            'status' => 'proposed',
            'nilai' => 100,
        ]);
        Subtask::insert([
            ['task_instance_id' => $task->id, 'judul' => 'Hitung gaji per karyawan', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Hitung potongan pajak', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Kirim daftar gaji ke HRD', 'is_done' => false],
        ]);

        // 3. Rutin Bulanan
        $task = TaskInstance::create([
            'judul_spesifik' => 'Laporan Pajak Bulanan',
            'deskripsi' => 'Menyusun laporan pajak untuk bulan ini',
            'jenis' => 'rutin',
            'recurrence_type' => 'monthly',
            'recurrence_day' => 15,
            'recurrence_month' => null,
            'generate_offset_days' => 5,
            'bobot' => 5,
            'deadline' => now()->addDays(14)->toDateString(),
            'assignee_id' => $ipin->id,
            'assignor_id' => $ipin->id,
            'initiation_type' => 'proposal',
            'status' => 'proposed',
            'nilai' => 100,
        ]);
        Subtask::insert([
            ['task_instance_id' => $task->id, 'judul' => 'Kumpulkan data pajak', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Hitung PPN dan PPH', 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Kirim laporan ke kantor pajak', 'is_done' => false],
        ]);

        // 4. Rutin Tahunan
        $task = TaskInstance::create([
            'judul_spesifik' => 'Audit Keuangan Tahunan',
            'deskripsi' => 'Mempersiapkan data untuk audit keuangan tahunan',
            'jenis' => 'rutin',
            'recurrence_type' => 'yearly',
            'recurrence_day' => 1,
            'recurrence_month' => 1,
            'generate_offset_days' => 30,
            'bobot' => 8,
            'deadline' => '2027-01-01',
            'assignee_id' => $ipin->id,
            'assignor_id' => $ipin->id,
            'initiation_type' => 'proposal',
            'status' => 'proposed',
            'nilai' => 100,
        ]);
        Subtask::insert([
            ['task_instance_id' => $task->id, 'judul' => 'Siapkan laporan keuangan', 'porsi' => 40, 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Siapkan bukti transaksi', 'porsi' => 30, 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Koordinasi dengan auditor', 'porsi' => 30, 'is_done' => false],
        ]);

        // 5. Proyek
        $task = TaskInstance::create([
            'judul_spesifik' => 'Implementasi Sistem Informasi Keuangan',
            'deskripsi' => 'Mengimplementasikan sistem informasi keuangan terintegrasi',
            'jenis' => 'proyek',
            'recurrence_type' => null,
            'recurrence_day' => null,
            'recurrence_month' => null,
            'generate_offset_days' => 0,
            'bobot' => 10,
            'tanggal_mulai' => '2026-06-01',
            'deadline' => '2026-12-31',
            'assignee_id' => $ipin->id,
            'assignor_id' => $ipin->id,
            'initiation_type' => 'proposal',
            'status' => 'proposed',
            'nilai' => 100,
        ]);
        Subtask::insert([
            ['task_instance_id' => $task->id, 'judul' => 'Analisis kebutuhan sistem', 'porsi' => 40, 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Pilih vendor software', 'porsi' => 20, 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Implementasi dan testing', 'porsi' => 30, 'is_done' => false],
            ['task_instance_id' => $task->id, 'judul' => 'Training pengguna', 'porsi' => 10, 'is_done' => false],
        ]);
    }
}
