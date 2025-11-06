<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_sensores', function (Blueprint $table) {
            $table->id('idSensor');
            $table->string('nomeSensor');
            $table->boolean('statusManual')->default(0)->comment('Simula se a vaga está ocupada (1) ou livre (0)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_sensores');
    }
};
