<?php

namespace App\Http\Controllers;

// Importações de classes que serão usadas neste controlador
use Illuminate\Http\Request;
use App\Models\Vaga;
use App\Models\Sensor;
use App\Models\VagaInteligente;
use App\Models\Projeto;
use App\Models\Setor;

/**
 * Controlador responsável por gerenciar as vagas inteligentes
 * Este controlador lida com a associação entre vagas e sensores
 */
class VagaInteligenteController extends Controller
{
    /**
     * Exibe a página principal de vagas inteligentes
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Busca todas as vagas e sensores para exibir na página
        $vagas = Vaga::all();
        $sensores = Sensor::all();
        
        // Retorna a view com os dados
        return view('vagasInteligentes.index', compact('vagas', 'sensores'));
    }

    /**
     * Retorna os dados das vagas inteligentes em formato JSON
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVagasInteligentes()
    {
        // Busca todas as vagas inteligentes com seus relacionamentos
        $vagasInteligentes = VagaInteligente::with(['vaga', 'sensor'])->get();
        
        // Formata os dados para retornar como JSON
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
     * Exibe a página para associar sensores a vagas
     * 
     * @return \Illuminate\View\View
     */
    public function associar()
    {
        // Busca todos os projetos para exibir na página
        $projetos = Projeto::all();
        
        // Retorna a view com os dados
        return view('vagasInteligentes', compact('projetos'));
    }
    
    /**
     * Exibe o painel de vagas inteligentes
     * 
     * @return \Illuminate\View\View
     */
    public function painelVagasInteligentes()
    {
        // Busca todos os projetos ordenados pelo nome
        $projetos = Projeto::orderBy('nomeProjeto')->get();
        
        // Retorna a view 'painelVagasInteligentes' passando a lista de projetos
        return view('painelVagasInteligentes', compact('projetos'));
    }
    
    /**
     * Retorna os setores de um projeto específico (para API)
     * 
     * @param int $idProjeto ID do projeto
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSetoresProjeto($idProjeto)
    {
        // Busca todos os setores que pertencem ao projeto especificado
        $setores = Setor::where('idProjeto', $idProjeto)->get();
        
        // Retorna os setores em formato JSON
        return response()->json($setores);
    }
    
    /**
     * Retorna os dados de um projeto específico (para API)
     * 
     * @param int $idProjeto ID do projeto
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProjeto($idProjeto)
    {
        // Busca o projeto pelo ID ou retorna erro 404 se não encontrar
        $projeto = Projeto::findOrFail($idProjeto);
        
        // Retorna os dados do projeto em formato JSON
        return response()->json($projeto);
    }
    
    /**
     * Retorna as vagas de um projeto específico (para API)
     * 
     * @param int $idProjeto ID do projeto
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVagasProjeto($idProjeto)
    {
        // Busca todas as vagas que pertencem a setores do projeto especificado
        // whereHas verifica se existe um relacionamento que atende à condição
        $vagas = Vaga::whereHas('setor', function($q) use ($idProjeto) {
            $q->where('idProjeto', $idProjeto);
        })
        // with carrega os relacionamentos para evitar múltiplas consultas
        ->with(['grids', 'vagaInteligente.sensor'])
        ->get();
        
        // Retorna as vagas em formato JSON
        return response()->json($vagas);
    }
}