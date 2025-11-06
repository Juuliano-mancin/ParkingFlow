<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_vagasInteligente', function (Blueprint $table) {
            $table->id('idVagaInteligente');
            
            $table->unsignedBigInteger('idVaga');
            $table->unsignedBigInteger('idSensor');

            // se quiser duplicar o statusManual aqui para facilitar os testes, descomente:
            // $table->boolean('statusManual')->default(0);

            $table->timestamps();

            // relações
            $table->foreign('idVaga')->references('idVaga')->on('tb_vagas')->onDelete('cascade');
            $table->foreign('idSensor')->references('idSensor')->on('tb_sensores')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_vagasInteligente');
    }
};
