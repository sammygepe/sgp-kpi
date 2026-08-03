<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('progress_logs', function (Blueprint $table) {
            $table->enum('activity_type', ['note', 'subtask', 'submit', 'approve', 'score'])->default('note');
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('progress_logs', function (Blueprint $table) {
            $table->dropColumn(['activity_type', 'description']);
        });
    }
};
