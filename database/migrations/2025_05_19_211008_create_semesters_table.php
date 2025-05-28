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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran'); // contoh: 2025/2026
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->unsignedInteger('no_semester'); // contoh: semester ke-1, 2, 3...
            $table->boolean('aktif')->default(false);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
