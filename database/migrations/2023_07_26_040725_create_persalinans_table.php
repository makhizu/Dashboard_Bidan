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
        Schema::create('persalinans', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->string('gpa')->nullable();
            $table->integer('usia_kehamilan')->nullable();
            $table->integer('umur')->nullable();
            $table->date('tanggal_persalinan')->nullable();
            $table->string('komplikasi')->nullable();
            $table->string('jk_bayi')->nullable();
            $table->integer('bb_bayi')->nullable();
            $table->float('pb_bayi', 8, 2)->nullable();
            $table->float('lk_bayi', 8, 2)->nullable();
            $table->float('ld_bayi', 8, 2)->nullable();            
            $table->string('stat_rujuk_ibu')->nullable();
            $table->string('stat_rujuk_bayi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persalinans');
    }
};
