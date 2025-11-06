<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VagaInteligente extends Model
{
    use HasFactory;

    protected $table = 'tb_vagasInteligente';
    protected $primaryKey = 'idVagaInteligente';

    protected $fillable = [
        'idVaga',
        'idSensor',
        // 'statusManual', // caso venha a ser usado para teste
    ];

    public $timestamps = true;

    // Relação com a Vaga
    public function vaga()
    {
        return $this->belongsTo(Vaga::class, 'idVaga', 'idVaga');
    }

    // Relação com o Sensor
    public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'idSensor', 'idSensor');
    }
}
