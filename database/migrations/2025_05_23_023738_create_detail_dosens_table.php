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
        Schema::create('detail_dosens', function (Blueprint $table) {
            $table->id();
            // Foreign key untuk user_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Kolom nama
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('jenis_kelamin')->nullable();

            // Menambahkan foreign key untuk jabatan_fungsional dan bidang_keahlian
            $table->foreignId('jabatan_fungsional_id')->nullable()->constrained('jabatan_fungsionals')->onDelete('set null');
            $table->foreignId('bidang_keahlian_id')->nullable()->constrained('bidang_keahlians')->onDelete('set null');

            // Kolom file_path (untuk menyimpan path file jika ada)
            $table->string('file_path')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_dosens');
    }
};
