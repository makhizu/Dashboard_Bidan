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
        Schema::create('imunisasi_d_t_l_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_anak')->nullable();
            $table->unsignedBigInteger('id_header');
            $table->string('imunisasi');
            $table->timestamps();

            // $table->foreign('id_header')->references('id')->on('imunisasi_h_d_r_s')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imunisasi_d_t_l_s');
    }
};
