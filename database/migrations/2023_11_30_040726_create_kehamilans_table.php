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
        Schema::create('kehamilans', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->date('tanggal_kunjungan');
            $table->integer('umur');
            $table->text('keluhan')->nullable();
            $table->string('gpa')->nullable();
            $table->integer('gravida')->nullable();
            $table->float('bb', 8, 1)->nullable();
            $table->integer('tb')->nullable();
            $table->string('tekanan_darah')->nullable();
            $table->float('lila', 8, 1)->nullable();
            $table->string('resti')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehamilans');
    }
};
