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
        Schema::create('file_bukti_penawaran_kompensasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penawaran_kompensasi_id')->constrained('penawaran_kompensasis')->onDelete('cascade');
            $table->string('file_path');
            $table->text('keterangan')->nullable(); // deskripsi singkat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_bukti_kompensasis');
    }
};
