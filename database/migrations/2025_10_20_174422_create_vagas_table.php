<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
        {
            Schema::create('tb_vagas', function (Blueprint $table) {
                $table->id('idVaga');
                $table->string('nomeVaga');
                $table->enum('tipoVaga', ['carro', 'moto', 'idoso', 'deficiente'])->default('carro');
                $table->unsignedBigInteger('idSetor');
                $table->foreign('idSetor')->references('idSetor')->on('tb_setores')->onDelete('cascade');
                $table->timestamps();
            });
        }

    public function down(): void
        {
            Schema::dropIfExists('tb_vagas');
        }
};
