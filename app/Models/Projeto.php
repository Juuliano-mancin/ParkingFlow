<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projeto extends Model
    {
        use HasFactory;

        protected $table = 'tb_projetos';

        protected $primaryKey = 'idProjeto';

        protected $fillable = [
            'idCliente',
            'nomeProjeto',
            'plantaEstacionamento',
            'caminhoPlantaEstacionamento'
        ];

        public $timestamps = true;

        public function cliente()
            {
                /* Define um relacionamento de "pertence a" (belongsTo) com a tabela Cliente, usando 'idCliente' como chave estrangeira e primária */
                return $this->belongsTo(Cliente::class, 'idCliente', 'idCliente'); 
            } 
    }