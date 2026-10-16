<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_factors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();      // BD14+, AD4-7, dll
            $table->integer('selisih_min');            // batas bawah (inklusif)
            $table->integer('selisih_max');            // batas atas (inklusif)
            $table->decimal('faktor', 3, 2);           // 0.10 - 1.50
            $table->string('label', 100);              // deskripsi
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_factors');
    }
};
