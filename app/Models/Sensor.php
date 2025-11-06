<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    protected $table = 'tb_sensores';
    protected $primaryKey = 'idSensor';

    protected $fillable = [
        'nomeSensor',
        'statusManual',
    ];

    public $timestamps = true;

    // Relação 1:N - um sensor pode estar vinculado a várias vagas inteligentes (se permitido)
    public function vagasInteligentes()
    {
        return $this->hasMany(VagaInteligente::class, 'idSensor', 'idSensor');
    }
}
