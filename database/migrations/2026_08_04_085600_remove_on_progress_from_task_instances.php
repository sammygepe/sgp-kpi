<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah semua task dengan status 'on_progress' menjadi 'approved'
        DB::statement("UPDATE task_instances SET status = 'approved' WHERE status = 'on_progress'");

        // 2. Ubah tipe kolom menjadi TEXT sementara agar enum bisa diubah
        DB::statement("ALTER TABLE task_instances ALTER COLUMN status TYPE TEXT");

        // 3. Hapus constraint enum lama
        DB::statement("ALTER TABLE task_instances DROP CONSTRAINT IF EXISTS task_instances_status_check");

        // 4. Tambahkan constraint enum baru (tanpa 'on_progress')
        DB::statement("ALTER TABLE task_instances ADD CONSTRAINT task_instances_status_check CHECK (status IN ('draft', 'proposed', 'approved', 'submitted', 'completed', 'rejected'))");

        // 5. Kembalikan tipe ke VARCHAR (opsional, biarkan TEXT lebih aman)
        // Tidak perlu diubah karena TEXT lebih fleksibel
    }

    public function down(): void
    {
        // Rollback: tambahkan 'on_progress' kembali
        DB::statement("ALTER TABLE task_instances DROP CONSTRAINT IF EXISTS task_instances_status_check");
        DB::statement("ALTER TABLE task_instances ADD CONSTRAINT task_instances_status_check CHECK (status IN ('draft', 'proposed', 'approved', 'on_progress', 'submitted', 'completed', 'rejected'))");
    }
};
