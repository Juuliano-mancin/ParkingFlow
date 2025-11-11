<?php

// Importações de classes que serão usadas neste arquivo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorDataController;
use App\Http\Controllers\VagaInteligenteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui é onde você registra as rotas da API para sua aplicação.
| Estas rotas são carregadas pelo RouteServiceProvider dentro de um grupo
| que recebe o middleware "api". Aproveite para construir sua API!
|
*/

// Rota padrão do Laravel para usuários autenticados
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas para a API de sensores ESP32
// O prefixo 'sensors' agrupa todas as rotas relacionadas a sensores
Route::prefix('sensors')->group(function () {
    // Rota para receber dados do ESP32 (protegida pelo middleware de autenticação)
    // POST /api/sensors/update - Endpoint para atualizar o status do sensor
    Route::post('/update', [SensorDataController::class, 'updateSensorData'])
        ->middleware('esp32.auth'); // Aplica o middleware de autenticação
    
    // Rotas públicas para consulta (não precisam de autenticação)
    // GET /api/sensors/status - Endpoint para obter o status de todos os sensores
    Route::get('/status', [SensorDataController::class, 'getSensorStatus']);
    
    // GET /api/sensors/vagas - Endpoint para obter o status de todas as vagas
    Route::get('/vagas', [SensorDataController::class, 'getVagasStatus']);
});

// Rotas para a interface web
// O prefixo 'projetos' agrupa todas as rotas relacionadas a projetos
Route::prefix('projetos')->group(function () {
    // GET /api/projetos/{idProjeto} - Endpoint para obter dados de um projeto específico
    Route::get('/{idProjeto}', [VagaInteligenteController::class, 'getProjeto']);
    
    // GET /api/projetos/{idProjeto}/setores - Endpoint para obter setores de um projeto
    Route::get('/{idProjeto}/setores', [VagaInteligenteController::class, 'getSetoresProjeto']);
    
    // GET /api/projetos/{idProjeto}/vagas - Endpoint para obter vagas de um projeto
    Route::get('/{idProjeto}/vagas', [VagaInteligenteController::class, 'getVagasProjeto']);
});