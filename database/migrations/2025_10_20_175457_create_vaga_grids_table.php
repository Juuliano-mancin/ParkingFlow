<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_vagasGrid', function (Blueprint $table) {
            $table->id('idGrid');
            $table->unsignedBigInteger('idVaga');
            $table->integer('posicaoVagaX');
            $table->integer('posicaoVagaY');
            $table->foreign('idVaga')->references('idVaga')->on('tb_vagas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_vagasGrid');
    }
};
