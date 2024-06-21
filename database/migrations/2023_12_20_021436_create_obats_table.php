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
        Schema::create('obats', function (Blueprint $table) {
            $table->id();
            $table->integer('no');
            $table->string('jenis_vaksin')->nullable();
            $table->integer('sisa_bulan_lalu')->nullable();
            $table->integer('penerimaan_bulan_ini')->nullable();
            $table->integer('jumlah')->nullable();
            $table->integer('pemakaian_bulan_ini')->nullable();
            $table->integer('sisa_bulan_ini')->nullable();
            $table->integer('permintaan_bulan_ini')->nullable();
            $table->integer('pemberian_bulan_ini')->nullable();
            $table->string('laporan_bulan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obats');
    }
};
