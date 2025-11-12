<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vaga;
use App\Models\Sensor;
use App\Models\VagaInteligente;
use App\Models\Projeto;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class VagaInteligenteController extends Controller
{
    /**
     * Exibe a tela para associar sensores às vagas.
     */
    public function associar()
    {
        $projetos = Projeto::all();
        return view('vagasInteligentes', compact('projetos'));
    }

    /**
     * Retorna todos os sensores disponíveis
     */
    public function getSensores()
    {
        $sensores = Sensor::all();
        return response()->json($sensores);
    }

    /**
     * Retorna todas as vagas inteligentes (associações existentes)
     */
    public function getVagasInteligentes()
    {
        $vagasInteligentes = VagaInteligente::with(['vaga', 'sensor'])->get();
        
        return response()->json($vagasInteligentes->map(function($vi) {
            return [
                'idVagaInteligente' => $vi->idVagaInteligente,
                'idVaga' => $vi->idVaga,
                'idSensor' => $vi->idSensor,
                'vaga' => $vi->vaga,
                'sensor' => $vi->sensor
            ];
        }));
    }

    /**
     * Exibe a lista de vagas inteligentes
     */
    public function index()
    {
        $vagas = Vaga::all();
        $sensores = Sensor::all();
        return view('vagasInteligentes.index', compact('vagas', 'sensores'));
    }

    /**
     * Associa um sensor a uma vaga específica.
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Dados recebidos para associação:', $request->all());

            $request->validate([
                'idVaga' => 'required|exists:tb_vagas,idVaga',
                'idSensor' => 'required|exists:tb_sensores,idSensor',
            ]);

            // Verifica se a vaga já tem um sensor associado
            $vagaExistente = VagaInteligente::where('idVaga', $request->idVaga)->first();
            if ($vagaExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta vaga já possui um sensor associado.'
                ], 400);
            }

            // Verifica se o sensor já está vinculado a outra vaga
            $sensorExistente = VagaInteligente::where('idSensor', $request->idSensor)->first();
            if ($sensorExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este sensor já está associado a outra vaga.'
                ], 400);
            }

            // Cria o vínculo
            $vagaInteligente = VagaInteligente::create([
                'idVaga' => $request->idVaga,
                'idSensor' => $request->idSensor,
            ]);

            \Log::info('Associação criada com sucesso:', $vagaInteligente->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Sensor associado à vaga com sucesso!',
                'data' => $vagaInteligente
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao associar sensor:', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualiza o status manual do sensor.
     */
    public function toggleStatus($idSensor, Request $request)
    {
        try {
            $sensor = Sensor::findOrFail($idSensor);
            $sensor->statusManual = $request->input('statusManual');
            $sensor->save();

            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso',
                'status' => $sensor->statusManual,
                'sensor' => $sensor
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar sensor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar sensor: ' . $e->getMessage()
            ], 500);
        }
    }

    // =========================================================================
    // MÉTODOS ESPECÍFICOS PARA INTEGRAÇÃO COM ARDUINO
    // =========================================================================

    /**
     * Método específico para Arduino atualizar status
     * POST /api/arduino/sensores/{id}/status
     */
    public function atualizarStatusArduino(Request $request, $id): JsonResponse
    {
        try {
            // Validação específica para dados do Arduino
            $request->validate([
                'statusManual' => 'required|boolean',
                'timestamp' => 'sometimes|date' // opcional para log
            ]);

            $sensor = Sensor::find($id);
            
            if (!$sensor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sensor não encontrado'
                ], 404);
            }

            // Log para debug
            Log::info('Arduino atualizando sensor', [
                'sensor_id' => $id,
                'status_anterior' => $sensor->statusManual,
                'status_novo' => $request->statusManual,
                'timestamp' => $request->timestamp ?? now()
            ]);

            // Atualiza apenas o campo necessário
            $sensor->update([
                'statusManual' => $request->statusManual
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso',
                'sensor' => [
                    'id' => $sensor->idSensor,
                    'nome' => $sensor->nomeSensor,
                    'status' => $sensor->statusManual,
                    'ocupado' => (bool)$sensor->statusManual
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Erro Arduino ao atualizar sensor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método para Arduino consultar status de todos os sensores
     * GET /api/arduino/sensores
     */
    public function getSensoresArduino(): JsonResponse
    {
        try {
            $sensores = Sensor::select('idSensor', 'nomeSensor', 'statusManual')
                             ->get()
                             ->map(function($sensor) {
                                 return [
                                     'id' => $sensor->idSensor,
                                     'nome' => $sensor->nomeSensor,
                                     'status' => (bool)$sensor->statusManual,
                                     'ocupado' => (bool)$sensor->statusManual // campo adicional para clareza
                                 ];
                             });

            return response()->json([
                'success' => true,
                'sensores' => $sensores,
                'total' => $sensores->count(),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Erro Arduino ao buscar sensores: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método para Arduino consultar status de um sensor específico
     * GET /api/arduino/sensores/{id}
     */
    public function getSensorArduino($id): JsonResponse
    {
        try {
            $sensor = Sensor::find($id);
            
            if (!$sensor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sensor não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'sensor' => [
                    'id' => $sensor->idSensor,
                    'nome' => $sensor->nomeSensor,
                    'status' => (bool)$sensor->statusManual,
                    'ocupado' => (bool)$sensor->statusManual
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Erro Arduino ao buscar sensor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint de saúde da API para Arduino
     * GET /api/arduino/status
     */
    public function statusArduino(): JsonResponse
    {
        return response()->json([
            'status' => 'online',
            'message' => 'API Arduino funcionando',
            'timestamp' => now()->toISOString(),
            'versao' => '1.0',
            'sensores_cadastrados' => Sensor::count()
        ]);
    }

    /**
     * Método para Arduino atualizar status em lote (múltiplos sensores)
     * POST /api/arduino/sensores/batch-update
     */
    public function atualizarStatusLoteArduino(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'sensores' => 'required|array',
                'sensores.*.id' => 'required|exists:tb_sensores,idSensor',
                'sensores.*.status' => 'required|boolean'
            ]);

            $resultados = [];
            
            foreach ($request->sensores as $sensorData) {
                $sensor = Sensor::find($sensorData['id']);
                $statusAnterior = $sensor->statusManual;
                
                $sensor->update(['statusManual' => $sensorData['status']]);
                
                $resultados[] = [
                    'id' => $sensor->idSensor,
                    'nome' => $sensor->nomeSensor,
                    'status_anterior' => $statusAnterior,
                    'status_novo' => $sensorData['status'],
                    'sucesso' => true
                ];
            }

            Log::info('Arduino atualizou sensores em lote', [
                'total_sensores' => count($request->sensores),
                'resultados' => $resultados
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lote de sensores atualizado com sucesso',
                'sensores_atualizados' => count($request->sensores),
                'resultados' => $resultados,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Erro Arduino ao atualizar lote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }
}