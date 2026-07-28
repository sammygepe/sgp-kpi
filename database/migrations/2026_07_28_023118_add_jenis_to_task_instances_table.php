<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('task_instances', function (Blueprint $table) {
            $table->enum('jenis', ['rutin', 'proyek'])->default('rutin')->after('judul_spesifik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_instances', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};
