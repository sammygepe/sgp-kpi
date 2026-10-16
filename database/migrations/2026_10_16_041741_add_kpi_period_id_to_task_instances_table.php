<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_instances', function (Blueprint $table) {
            $table->foreignId('kpi_period_id')
                ->nullable()
                ->after('status')
                ->constrained('kpi_periods')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('task_instances', function (Blueprint $table) {
            $table->dropForeign(['kpi_period_id']);
            $table->dropColumn('kpi_period_id');
        });
    }
};
