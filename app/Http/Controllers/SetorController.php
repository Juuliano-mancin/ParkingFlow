<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setor;
use App\Models\Projeto;
use Illuminate\Support\Facades\DB;

class SetorController extends Controller
{
    public function create($idProjeto)
    {
        $projeto = Projeto::findOrFail($idProjeto);

        // Garante que o caminho da planta esteja correto
        $caminhoPublico = 'storage/' . ltrim($projeto->caminhoPlantaEstacionamento, '/');

        return view('novoSetor', [
            'projeto' => $projeto,
            'caminhoPublico' => $caminhoPublico,
        ]);
    }

    public function store(Request $request)
    {
        // Validação
        $data = $request->validate([
            'idProjeto' => 'required|integer|exists:tb_projetos,idProjeto',
            'setores' => 'required|array|min:1',
            'setores.*.nomeSetor' => 'required|string',
            'setores.*.corSetor' => 'required|string',
            'setores.*.x' => 'required|integer',
            'setores.*.y' => 'required|integer',
        ]);

        try 
            {
                // Salva setores dentro de uma transação
                DB::transaction(function() use ($data) {
                    foreach ($data['setores'] as $setor) {
                        Setor::create([
                            'idProjeto' => $data['idProjeto'],
                            'nomeSetor' => $setor['nomeSetor'],
                            'corSetor' => $setor['corSetor'],
                            'setorCoordenadaX' => $setor['x'],
                            'setorCoordenadaY' => $setor['y'],
                        ]);
                    }
                });

                return response()->json
                    ([
                    'success' => true,
                    'message' => 'Setores salvos com sucesso!',
                    'idProjeto' => $data['idProjeto']
                    ]);
            } 
        catch (\Exception $e) 
            {
                \Log::error('Erro ao salvar setores: '.$e->getMessage());
                return response()->json(['success' => false, 'message' => 'Erro ao salvar setores.'], 500);
            }
    }

    public function getSetores($idProjeto)
        {
            $setores = \App\Models\Setor::where('idProjeto', $idProjeto)->get();
            return response()->json($setores);
        }
}
