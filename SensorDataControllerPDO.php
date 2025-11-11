<?php

namespace App\Http\Controllers\Api;

// Importações de classes que serão usadas neste controlador
use App\Http\Controllers\Controller; // Classe base para controladores
use Illuminate\Http\Request; // Classe para manipular requisições HTTP
use Illuminate\Support\Facades\DB; // Classe para operações de banco de dados
use Illuminate\Support\Facades\Log; // Classe para registrar logs
use PDO; // Classe PHP Data Objects para acesso ao banco de dados

/**
 * Controlador responsável por gerenciar os dados dos sensores ESP32
 * Esta versão usa PDO e STMT explicitamente para operações de banco de dados
 */
class SensorDataControllerPDO extends Controller
{
    /**
     * Recebe dados do sensor ESP32 e atualiza o status no banco de dados usando PDO e STMT
     * 
     * @param Request $request Requisição HTTP contendo os dados do sensor
     * @return \Illuminate\Http\JsonResponse Resposta em formato JSON
     */
    public function updateSensorData(Request $request)
    {
        try {
            // Registra os dados recebidos no log para facilitar a depuração
            Log::info('Dados recebidos do ESP32:', $request->all());
            
            // Valida os dados recebidos na requisição
            $validated = $request->validate([
                'sensor_id' => 'required|exists:tb_sensores,idSensor',
                'status' => 'required|boolean', // 0 = livre, 1 = ocupado
            ]);
            
            // Obtém uma conexão PDO do Laravel
            $pdo = DB::connection()->getPdo();
            
            // Prepara a consulta SQL para atualizar o sensor (STMT = Statement)
            $stmt = $pdo->prepare("UPDATE tb_sensores SET statusManual = :status, updated_at = NOW() WHERE idSensor = :sensor_id");
            
            // Vincula os parâmetros à consulta (evita injeção de SQL)
            // PDO::PARAM_INT especifica que o valor é um inteiro
            $stmt->bindParam(':status', $validated['status'], PDO::PARAM_INT);
            $stmt->bindParam(':sensor_id', $validated['sensor_id'], PDO::PARAM_INT);
            
            // Executa a consulta SQL
            $result = $stmt->execute();
            
            // Verifica se a atualização foi bem-sucedida
            if ($result) {
                // Prepara uma consulta para buscar o sensor atualizado
                $sensorQuery = $pdo->prepare("SELECT * FROM tb_sensores WHERE idSensor = :sensor_id");
                
                // Vincula o parâmetro à consulta
                $sensorQuery->bindParam(':sensor_id', $validated['sensor_id'], PDO::PARAM_INT);
                
                // Executa a consulta
                $sensorQuery->execute();
                
                // Busca o resultado como um array associativo
                $sensor = $sensorQuery->fetch(PDO::FETCH_ASSOC);
                
                // Retorna uma resposta JSON com status 200 (OK)
                return response()->json([
                    'success' => true,
                    'message' => 'Dados do sensor atualizados com sucesso',
                    'data' => $sensor
                ]);
            } else {
                // Se a atualização falhou, retorna erro 500
                return response()->json([
                    'success' => false,
                    'message' => 'Falha ao atualizar dados do sensor'
                ], 500);
            }
            
        } catch (\Exception $e) {
            // Captura qualquer exceção que ocorra durante o processo
            Log::error('Erro ao processar dados do sensor: ' . $e->getMessage());
            
            // Retorna uma resposta JSON com status 500 (Erro interno do servidor)
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar dados do sensor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Retorna o status atual de todos os sensores usando PDO e STMT
     * 
     * @return \Illuminate\Http\JsonResponse Resposta em formato JSON com a lista de sensores
     */
    public function getSensorStatus()
    {
        try {
            // Obtém uma conexão PDO do Laravel
            $pdo = DB::connection()->getPdo();
            
            // Prepara a consulta SQL para buscar os sensores
            $stmt = $pdo->prepare("SELECT idSensor, nomeSensor, statusManual FROM tb_sensores");
            
            // Executa a consulta
            $stmt->execute();
            
            // Busca todos os resultados como um array de arrays associativos
            $sensores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Retorna uma resposta JSON com os dados dos sensores
            return response()->json([
                'success' => true,
                'data' => $sensores
            ]);
            
        } catch (\Exception $e) {
            // Captura qualquer exceção que ocorra durante o processo
            Log::error('Erro ao buscar status dos sensores: ' . $e->getMessage());
            
            // Retorna uma resposta JSON com status 500 (Erro interno do servidor)
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar status dos sensores',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Retorna o status das vagas inteligentes usando PDO e STMT
     * 
     * @return \Illuminate\Http\JsonResponse Resposta em formato JSON com a lista de vagas inteligentes
     */
    public function getVagasStatus()
    {
        try {
            // Obtém uma conexão PDO do Laravel
            $pdo = DB::connection()->getPdo();
            
            // Prepara a consulta SQL para buscar as vagas inteligentes com seus relacionamentos
            // Esta consulta JOIN une as tabelas de vagas inteligentes, vagas e sensores
            $stmt = $pdo->prepare("
                SELECT 
                    vi.idVagaInteligente,
                    v.idVaga,
                    v.nomeVaga,
                    v.tipoVaga,
                    v.idSetor,
                    s.idSensor,
                    s.nomeSensor,
                    s.statusManual
                FROM 
                    tb_vagasInteligente vi
                    LEFT JOIN tb_vagas v ON vi.idVaga = v.idVaga
                    LEFT JOIN tb_sensores s ON vi.idSensor = s.idSensor
            ");
            
            // Executa a consulta
            $stmt->execute();
            
            // Busca todos os resultados como um array de arrays associativos
            $vagasInteligentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Retorna uma resposta JSON com os dados das vagas
            return response()->json([
                'success' => true,
                'data' => $vagasInteligentes
            ]);
            
        } catch (\Exception $e) {
            // Captura qualquer exceção que ocorra durante o processo
            Log::error('Erro ao buscar status das vagas: ' . $e->getMessage());
            
            // Retorna uma resposta JSON com status 500 (Erro interno do servidor)
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar status das vagas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}