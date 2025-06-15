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
        Schema::create('kompensasis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dosen_matakuliah_id')->constrained('dosen_matakuliahs')->onDelete('cascade');
            $table->integer('menit_kompensasi');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('semester_lokal');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kompensasis');
    }
};
