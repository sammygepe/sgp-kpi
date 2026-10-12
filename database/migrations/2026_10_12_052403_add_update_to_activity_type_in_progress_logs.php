<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE progress_logs DROP CONSTRAINT IF EXISTS progress_logs_activity_type_check");
        DB::statement("ALTER TABLE progress_logs ADD CONSTRAINT progress_logs_activity_type_check CHECK (activity_type IN ('note', 'subtask', 'submit', 'approve', 'score', 'update'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE progress_logs DROP CONSTRAINT IF EXISTS progress_logs_activity_type_check");
        DB::statement("ALTER TABLE progress_logs ADD CONSTRAINT progress_logs_activity_type_check CHECK (activity_type IN ('note', 'subtask', 'submit', 'approve', 'score'))");
    }
};
