<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('penawaran_kompensasi_users', function (Blueprint $table) {
            $table->string('file_path')->nullable()->default(null)->change();
            $table->text('keterangan')->nullable()->default(null)->change();
        });
    }

    public function down()
    {
        Schema::table('penawaran_kompensasi_users', function (Blueprint $table) {
            $table->string('file_path')->nullable(false)->change();
            $table->text('keterangan')->nullable(false)->change();
        });
    }

};
