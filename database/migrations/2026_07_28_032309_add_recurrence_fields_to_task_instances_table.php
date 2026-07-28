<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_instances', function (Blueprint $table) {
            $table->enum('recurrence_type', ['daily', 'weekly', 'monthly', 'yearly'])->nullable()->after('jenis');
            $table->integer('recurrence_day')->nullable()->after('recurrence_type');
            $table->integer('recurrence_month')->nullable()->after('recurrence_day');
        });
    }

    public function down(): void
    {
        Schema::table('task_instances', function (Blueprint $table) {
            $table->dropColumn(['recurrence_type', 'recurrence_day', 'recurrence_month']);
        });
    }
};
