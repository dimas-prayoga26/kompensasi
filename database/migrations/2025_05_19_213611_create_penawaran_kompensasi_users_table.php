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
        Schema::create('penawaran_kompensasi_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penawaran_kompensasi_id')->constrained('penawaran_kompensasis')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('file_path');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'reject', 'accept'])->nullable()->default('pending'); // null + default
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penawaran_kompensasi_users');
    }
};
