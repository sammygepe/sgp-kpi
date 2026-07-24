<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_id')->nullable()->constrained('task_masters')->nullOnDelete();
            $table->string('judul_spesifik');
            $table->text('deskripsi')->nullable();
            $table->integer('bobot')->default(3);
            $table->date('deadline');
            $table->foreignId('assignee_id')->constrained('users');
            $table->foreignId('assignor_id')->constrained('users');
            $table->enum('initiation_type', ['proposal', 'direct'])->default('proposal');
            $table->enum('status', ['draft', 'proposed', 'approved', 'on_progress', 'submitted', 'completed', 'rejected'])->default('draft');
            $table->integer('progress')->default(0);
            $table->date('tanggal_submit')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('nilai')->default(100);
            $table->decimal('faktor_waktu', 3, 2)->nullable();
            $table->decimal('skor_akhir', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_instances');
    }
};
