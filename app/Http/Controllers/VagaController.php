<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Vaga;
use App\Models\VagaGrid;
use App\Models\Setor;
use App\Models\Projeto;

class VagaController extends Controller
{
    
    public function create($idProjeto)
        {
            $projeto = Projeto::findOrFail($idProjeto);
            $setores = Setor::where('idProjeto', $idProjeto)->get();

            $caminhoPublico = 'storage/' . ltrim($projeto->caminhoPlantaEstacionamento, '/');

            return view('novaVaga', [
                'projeto' => $projeto,
                'setores' => $setores,
                'caminhoPublico' => $caminhoPublico,
            ]);
        }

    
    public function store(Request $request)
    {
        // Aceita payload em lote: { idProjeto?: int, vagas: [ { idSetor, nomeVaga, tipoVaga?, coordenadas: [ { x, y } ] }, ... ] }
        $data = $request->validate([
            'idProjeto' => 'nullable|integer|exists:tb_projetos,idProjeto',
            'vagas' => 'required|array|min:1',
            'vagas.*.idSetor' => 'required|integer|exists:tb_setores,idSetor',
            'vagas.*.nomeVaga' => 'required|string|max:255',
            'vagas.*.tipoVaga' => 'nullable|string|in:carro,moto,idoso,deficiente',
            'vagas.*.coordenadas' => 'required|array|min:1',
            'vagas.*.coordenadas.*.x' => 'required|integer',
            'vagas.*.coordenadas.*.y' => 'required|integer',
        ]);

        try {
            DB::transaction(function() use ($data) {
                
                foreach ($data['vagas'] as $vagaData) {
                    $vaga = Vaga::create([
                        'idSetor' => $vagaData['idSetor'],
                        'nomeVaga' => $vagaData['nomeVaga'],
                        'tipoVaga' => $vagaData['tipoVaga'] ?? 'carro',
                    ]);

                    
                    foreach ($vagaData['coordenadas'] as $coord) {
                        VagaGrid::create([
                            'idVaga' => $vaga->idVaga,
                            'posicaoVagaX' => $coord['x'],
                            'posicaoVagaY' => $coord['y'],
                        ]);
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'Vagas salvas com sucesso!']);
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar vagas: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao salvar vagas.'], 500);
        }
    }

    
    public function listar($idProjeto)
        {
            $vagas = Vaga::whereHas('setor', fn($q) => $q->where('idProjeto', $idProjeto))
                ->with('grids')
                ->get();

            return response()->json($vagas);
        }
}
