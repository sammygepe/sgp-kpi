<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_masters', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->integer('bobot')->default(3);
            $table->enum('jenis', ['rutin', 'proyek'])->default('rutin');
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence_type', ['daily', 'weekly', 'monthly', 'yearly', 'custom'])->nullable();
            $table->integer('recurrence_day')->nullable();
            $table->integer('recurrence_month')->nullable();
            $table->integer('recurrence_interval')->nullable();
            $table->integer('generate_offset_days')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_masters');
    }
};
