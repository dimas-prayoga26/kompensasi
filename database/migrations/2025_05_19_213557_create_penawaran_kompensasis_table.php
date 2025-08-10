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
        Schema::create('penawaran_kompensasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');
            $table->text('deskripsi_kompensasi');
            $table->string('file_path');
            $table->integer('jumlah_mahasiswa')->default(1);
            $table->integer('jumlah_menit_kompensasi');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penawaran_kompensasis');
    }
};
