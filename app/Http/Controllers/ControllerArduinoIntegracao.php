<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ControllerArduinoIntegracao extends Controller
{
    /**
     * Buscar todos os sensores - Arduino pode consultar status
     */
    public function index(): JsonResponse
    {
        $sensores = Sensor::all();
        return response()->json($sensores);
    }

    /**
     * Buscar um sensor específico por ID
     */
    public function show($id): JsonResponse
    {
        $sensor = Sensor::find($id);
        
        if (!$sensor) {
            return response()->json(['error' => 'Sensor não encontrado'], 404);
        }
        
        return response()->json($sensor);
    }

    /**
     * Atualizar status do sensor - Arduino envia dados
     */
    public function update(Request $request, $id): JsonResponse
    {
        // Validação dos dados recebidos do Arduino
        $request->validate([
            'statusManual' => 'required|boolean',
            'nomeSensor' => 'sometimes|string'
        ]);

        $sensor = Sensor::find($id);
        
        if (!$sensor) {
            return response()->json(['error' => 'Sensor não encontrado'], 404);
        }

        $sensor->update($request->all());
        
        return response()->json([
            'message' => 'Sensor atualizado com sucesso',
            'sensor' => $sensor
        ]);
    }

    /**
     * Endpoint simples para verificação de status da API
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'status' => 'API funcionando',
            'timestamp' => now()
        ]);
    }
}