<?php

use App\Http\Controllers\VagaInteligenteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// =========================================================================
// ROTAS EXISTENTES DO SEU SISTEMA (mantenha essas)
// =========================================================================
Route::get('/sensores', [VagaInteligenteController::class, 'getSensores']);
Route::put('/sensores/{sensor}/toggle-status', [VagaInteligenteController::class, 'toggleStatus']);
Route::get('/setores/{idProjeto}', [SetorController::class, 'getSetores']);
Route::get('/vagas-inteligentes', [VagaInteligenteController::class, 'getVagasInteligentes']);

// =========================================================================
// ROTAS ESPECÍFICAS PARA INTEGRAÇÃO COM ARDUINO
// =========================================================================
Route::prefix('arduino')->group(function () {
    
    // Status da API Arduino - Verifica se está online
    Route::get('/status', [VagaInteligenteController::class, 'statusArduino']);
    
    // Consultar todos os sensores (formato simplificado para Arduino)
    Route::get('/sensores', [VagaInteligenteController::class, 'getSensoresArduino']);
    
    // Consultar um sensor específico
    Route::get('/sensores/{id}', [VagaInteligenteController::class, 'getSensorArduino']);
    
    // Atualizar status de um sensor individual
    Route::post('/sensores/{id}/status', [VagaInteligenteController::class, 'atualizarStatusArduino']);
    
    // Atualizar múltiplos sensores em lote (eficiente para muitos sensores)
    Route::post('/sensores/batch-update', [VagaInteligenteController::class, 'atualizarStatusLoteArduino']);
});

// =========================================================================
// ROTA FALLBACK PARA API (opcional - para tratar rotas não encontradas)
// =========================================================================
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint da API não encontrado. Verifique a documentação.',
        'timestamp' => now()->toISOString()
    ], 404);
});