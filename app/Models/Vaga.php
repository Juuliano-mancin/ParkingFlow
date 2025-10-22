<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaga extends Model
{
    use HasFactory;

    protected $table = 'tb_vagas';
    protected $primaryKey = 'idVaga';
    protected $fillable = ['nomeVaga', 'tipoVaga', 'idSetor'];

    public function setor()
    {
        return $this->belongsTo(Setor::class, 'idSetor', 'idSetor');
    }

    public function grids()
    {
        return $this->hasMany(VagaGrid::class, 'idVaga', 'idVaga');
    }
}
