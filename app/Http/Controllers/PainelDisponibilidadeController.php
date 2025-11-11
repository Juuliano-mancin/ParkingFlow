<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projeto;
use App\Models\Setor;
use App\Models\Vaga;

class PainelDisponibilidadeController extends Controller
{
    public function index(Request $request)
    {
        // Lista de projetos (para o dropdown)
        $projetos = Projeto::orderBy('nomeProjeto')->get();

        // Se o usuário selecionou um projeto, carrega seus setores e vagas DISPONÍVEIS
        $setores = collect();
        if ($request->has('projeto') && !empty($request->projeto)) {
            $setores = Setor::with('projeto')
                ->where('idProjeto', $request->projeto)
                ->withCount([
                    'vagas as vagas_carro' => function ($q) {
                        $q->where('tipoVaga', 'carro')
                          ->whereDoesntHave('vagaInteligente.sensor', function ($query) {
                              $query->where('statusManual', 1); // Vagas LIVRES (statusManual = 0)
                          });
                    },
                    'vagas as vagas_moto' => function ($q) {
                        $q->where('tipoVaga', 'moto')
                          ->whereDoesntHave('vagaInteligente.sensor', function ($query) {
                              $query->where('statusManual', 1);
                          });
                    },
                    'vagas as vagas_deficiente' => function ($q) {
                        $q->where('tipoVaga', 'deficiente')
                          ->whereDoesntHave('vagaInteligente.sensor', function ($query) {
                              $query->where('statusManual', 1);
                          });
                    },
                    'vagas as vagas_idoso' => function ($q) {
                        $q->where('tipoVaga', 'idoso')
                          ->whereDoesntHave('vagaInteligente.sensor', function ($query) {
                              $query->where('statusManual', 1);
                          });
                    },
                ])
                ->distinct()
                ->get();
        }

        return view('painelDisponibilidade', compact('projetos', 'setores'));
    }
}