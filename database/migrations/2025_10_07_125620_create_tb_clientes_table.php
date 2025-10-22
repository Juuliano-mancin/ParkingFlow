<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
    {
        public function up(): void
            {
                Schema::create('tb_clientes', function (Blueprint $table) {
                    $table->id('idCliente');
                    $table->string('nomeCliente')->nullable(false);
                    $table->string('razaoSocial')->nullable(false);
                    $table->enum('setorAtuacao', ['comercial', 'industrial', 'residencial', 'outros'])->default('comercial');
                    $table->string('segmentoAtuacao')->nullable(false);
                    $table->string('representanteCliente')->nullable(false);
                    $table->string('emailCliente')->nullable();
                    $table->bigInteger('contatoCliente')->nullable();
                    $table->string('cepCliente')->nullable();
                    $table->string('logradouroCliente')->nullable();
                    $table->string('numeroCliente')->nullable();
                    $table->string('complementoCliente')->nullable();
                    $table->string('cidadeCliente')->nullable();
                    $table->string('ufCliente', 2)->nullable();
                    $table->timestamps();
                });
            }

        public function down(): void
            {
                Schema::dropIfExists('tb_clientes');
            }
    };