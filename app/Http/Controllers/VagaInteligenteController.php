<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vaga;
use App\Models\Sensor;
use App\Models\VagaInteligente;
use App\Models\Projeto;

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
    public function toggleStatus($idSensor)
    {
        $sensor = Sensor::findOrFail($idSensor);
        $sensor->statusManual = !$sensor->statusManual;
        $sensor->save();

        return response()->json([
            'success' => true,
            'status' => $sensor->statusManual,
        ]);
    }
}