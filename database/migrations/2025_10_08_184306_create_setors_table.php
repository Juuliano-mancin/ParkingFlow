<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_setores', function (Blueprint $table) {
            $table->id('idSetor'); // PK Auto Increment
            $table->unsignedBigInteger('idProjeto'); // FK para projeto
            $table->string('nomeSetor');
            $table->string('corSetor'); // código da cor
            $table->integer('setorCoordenadaX')->nullable(); // coordenadas X do setor
            $table->integer('setorCoordenadaY')->nullable(); // coordenadas Y do setor
            $table->timestamps();

            // Chave estrangeira
            $table->foreign('idProjeto')->references('idProjeto')->on('tb_projetos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_setores');
    }
};