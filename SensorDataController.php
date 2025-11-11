<?php

namespace App\Http\Controllers\Api;

// Importações de classes que serão usadas neste controlador
use App\Http\Controllers\Controller; // Classe base para controladores
use Illuminate\Http\Request; // Classe para manipular requisições HTTP
use App\Models\Sensor; // Modelo do sensor
use App\Models\VagaInteligente; // Modelo da vaga inteligente
use Illuminate\Support\Facades\Log; // Classe para registrar logs

/**
 * Controlador responsável por gerenciar os dados dos sensores ESP32
 * Este controlador recebe dados dos sensores e fornece endpoints para consulta de status
 */
class SensorDataController extends Controller
{
    /**
     * Recebe dados do sensor ESP32 e atualiza o status no banco de dados
     * 
     * @param Request $request Requisição HTTP contendo os dados do sensor
     * @return \Illuminate\Http\JsonResponse Resposta em formato JSON
     */
    public function updateSensorData(Request $request)
    {
        try {
            // Registra os dados recebidos no log para facilitar a depuração
            // É útil para verificar se os dados estão chegando corretamente
            Log::info('Dados recebidos do ESP32:', $request->all());
            
            // Valida os dados recebidos na requisição
            // 'required' significa que o campo é obrigatório
            // 'exists' verifica se o sensor_id existe na tabela tb_sensores
            // 'boolean' verifica se o status é 0 ou 1
            $validated = $request->validate([
                'sensor_id' => 'required|exists:tb_sensores,idSensor',
                'status' => 'required|boolean', // 0 = livre, 1 = ocupado
            ]);
            
            // Busca o sensor no banco de dados pelo ID
            // findOrFail lança uma exceção se o sensor não for encontrado
            $sensor = Sensor::findOrFail($validated['sensor_id']);
            
            // Atualiza o status do sensor com o valor recebido
            $sensor->statusManual = $validated['status'];
            
            // Salva as alterações no banco de dados
            $sensor->save();
            
            // Retorna uma resposta JSON com status 200 (OK)
            return response()->json([
                'success' => true, // Indica que a operação foi bem-sucedida
                'message' => 'Dados do sensor atualizados com sucesso', // Mensagem amigável
                'data' => $sensor // Retorna os dados do sensor atualizado
            ]);
            
        } catch (\Exception $e) {
            // Captura qualquer exceção que ocorra durante o processo
            
            // Registra o erro no log do sistema
            Log::error('Erro ao processar dados do sensor: ' . $e->getMessage());
            
            // Retorna uma resposta JSON com status 500 (Erro interno do servidor)
            return response()->json([
                'success' => false, // Indica que a operação falhou
                'message' => 'Erro ao processar dados do sensor', // Mensagem amigável
                'error' => $e->getMessage() // Detalhes do erro para depuração
            ], 500);
        }
    }
    
    /**
     * Retorna o status atual de todos os sensores
     * 
     * @return \Illuminate\Http\JsonResponse Resposta em formato JSON com a lista de sensores
     */
    public function getSensorStatus()
    {
        try {
            // Busca todos os sensores no banco de dados
            // Seleciona apenas os campos necessários para economizar recursos
            $sensores = Sensor::all(['idSensor', 'nomeSensor', 'statusManual']);
            
            // Retorna uma resposta JSON com os dados dos sensores
            return response()->json([
                'success' => true, // Indica que a operação foi bem-sucedida
                'data' => $sensores // Lista de sensores
            ]);
            
        } catch (\Exception $e) {
            // Captura qualquer exceção que ocorra durante o processo
            
            // Registra o erro no log do sistema
            Log::error('Erro ao buscar status dos sensores: ' . $e->getMessage());
            
            // Retorna uma resposta JSON com status 500 (Erro interno do servidor)
            return response()->json([
                'success' => false, // Indica que a operação falhou
                'message' => 'Erro ao buscar status dos sensores', // Mensagem amigável
                'error' => $e->getMessage() // Detalhes do erro para depuração
            ], 500);
        }
    }
    
    /**
     * Retorna o status das vagas inteligentes
     * 
     * @return \Illuminate\Http\JsonResponse Resposta em formato JSON com a lista de vagas inteligentes
     */
    public function getVagasStatus()
    {
        try {
            // Busca todas as vagas inteligentes com seus relacionamentos
            // 'with' carrega os modelos relacionados (vaga e sensor) para evitar múltiplas consultas
            $vagasInteligentes = VagaInteligente::with(['vaga', 'sensor'])
                ->get() // Executa a consulta e retorna os resultados
                ->map(function($vi) { // Transforma cada resultado para o formato desejado
                    return [
                        'idVaga' => $vi->idVaga, // ID da vaga
                        'nomeVaga' => $vi->vaga->nomeVaga ?? null, // Nome da vaga (se existir)
                        'tipoVaga' => $vi->vaga->tipoVaga ?? null, // Tipo da vaga (se existir)
                        'idSetor' => $vi->vaga->idSetor ?? null, // ID do setor (se existir)
                        'idSensor' => $vi->idSensor, // ID do sensor
                        'nomeSensor' => $vi->sensor->nomeSensor ?? null, // Nome do sensor (se existir)
                        'status' => $vi->sensor->statusManual ?? null, // Status do sensor (0 = livre, 1 = ocupado)
                    ];
                });
            
            // Retorna uma resposta JSON com os dados das vagas
            return response()->json([
                'success' => true, // Indica que a operação foi bem-sucedida
                'data' => $vagasInteligentes // Lista de vagas inteligentes
            ]);
            
        } catch (\Exception $e) {
            // Captura qualquer exceção que ocorra durante o processo
            
            // Registra o erro no log do sistema
            Log::error('Erro ao buscar status das vagas: ' . $e->getMessage());
            
            // Retorna uma resposta JSON com status 500 (Erro interno do servidor)
            return response()->json([
                'success' => false, // Indica que a operação falhou
                'message' => 'Erro ao buscar status das vagas', // Mensagem amigável
                'error' => $e->getMessage() // Detalhes do erro para depuração
            ], 500);
        }
    }
}