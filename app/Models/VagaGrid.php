<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VagaGrid extends Model
{
    use HasFactory;

    protected $table = 'tb_vagasGrid';
    protected $primaryKey = 'idGrid';
    protected $fillable = ['idVaga', 'posicaoVagaX', 'posicaoVagaY'];

    public function vaga()
    {
        return $this->belongsTo(Vaga::class, 'idVaga', 'idVaga');
    }
}
