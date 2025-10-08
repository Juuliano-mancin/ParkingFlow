<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'tb_clientes';

    protected $primaryKey = 'idCliente';

    protected $fillable = [
        'nomeCliente',
        'razaoSocial',
        'setorAtuacao',
        'segmentoAtuacao',
        'representanteCliente',
        'emailCliente',
        'contatoCliente',
        'cepCliente',
        'logradouroCliente',
        'numeroCliente',
        'complementoCliente',
        'cidadeCliente',
        'ufCliente'
    ];

    public $timestamps = true;
}