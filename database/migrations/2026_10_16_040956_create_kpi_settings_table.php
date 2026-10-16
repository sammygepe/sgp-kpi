<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('grace_period_days')->default(30);           // Grace period default
            $table->integer('grace_period_december_days')->default(7);   // Khusus Desember
            $table->decimal('max_time_factor_floor', 3, 2)->default(0.10); // Faktor waktu minimum
            $table->boolean('auto_close_enabled')->default(true);        // Auto-close aktif
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_settings');
    }
};
