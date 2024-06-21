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
        Schema::create('anaks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bayi');
            $table->string('nik');
            $table->date('tanggal_lahir')->nullable();
            $table->float('pb_lahir', 8, 2)->nullable();
            $table->integer('bb_lahir')->nullable();
            $table->string('tempat_lahir');
            $table->string('jenis_kelamin', 3);
            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anaks');
    }
};
