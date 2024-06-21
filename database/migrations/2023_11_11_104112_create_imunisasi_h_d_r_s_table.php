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
        Schema::create('imunisasi_h_d_r_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_anak');
            $table->string('nik');
            $table->date('tanggal_kunjungan');
            $table->integer('bb');
            $table->string('umur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imunisasi_h_d_r_s');
    }
};
