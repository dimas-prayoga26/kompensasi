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
        Schema::create('dosen_matakuliahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('matakuliah_id')->constrained('matakuliahs')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_matakuliahs');
    }
};
