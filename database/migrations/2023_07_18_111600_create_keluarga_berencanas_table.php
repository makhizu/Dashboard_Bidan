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
        Schema::create('keluarga_berencanas', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->integer('umur')->nullable();            
            $table->date('tanggal_kunjungan');
            $table->string('jumlah_anak');
            $table->integer('akseptor');
            $table->string('mow')->nullable();
            $table->string('iud')->nullable();
            $table->string('suntik')->nullable();
            $table->string('pil')->nullable();
            $table->string('kondom')->nullable();
            $table->string('kunjungan');
            $table->string('empat_T')->nullable();
            $table->string('alki')->nullable();
            $table->text('efek_samping')->nullable();
            $table->text('komplikasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluarga_berencanas');
    }
};
