<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
    {
        public function up(): void
            {
                Schema::create('tb_projetos', function (Blueprint $table) {
                    $table->id('idProjeto');
                    $table->unsignedBigInteger('idCliente');
                    $table->string('nomeProjeto');
                    $table->string('plantaEstacionamento')->nullable();
                    $table->string('caminhoPlantaEstacionamento')->nullable();
                    $table->timestamps();

                    $table->foreign('idCliente')->references('idCliente')->on('tb_clientes')->onDelete('cascade');
                });
            }

        public function down(): void
            {
                Schema::dropIfExists('tb_projetos');
            }
    };