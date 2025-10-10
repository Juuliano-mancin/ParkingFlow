<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    use HasFactory;

    protected $table = 'tb_setores';
    protected $primaryKey = 'idSetor';
    protected $fillable = [
        'idProjeto',
        'nomeSetor',
        'corSetor',
        'setorCoordenadaX',
        'setorCoordenadaY'
    ];

    public $timestamps = true;

    public function projeto()
    {
        return $this->belongsTo(\App\Models\Projeto::class, 'idProjeto', 'idProjeto');
    }
}