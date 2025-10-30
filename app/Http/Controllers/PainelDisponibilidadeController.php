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

        // Se o usuário selecionou um projeto, carrega seus setores e vagas
        $setores = collect();
        if ($request->has('projeto') && !empty($request->projeto)) {
            $setores = Setor::with('projeto')
                ->where('idProjeto', $request->projeto)
                ->withCount([
                    'vagas as vagas_carro' => function ($q) {
                        $q->where('tipoVaga', 'carro');
                    },
                    'vagas as vagas_moto' => function ($q) {
                        $q->where('tipoVaga', 'moto');
                    },
                    'vagas as vagas_deficiente' => function ($q) {
                        $q->where('tipoVaga', 'deficiente');
                    },
                    'vagas as vagas_idoso' => function ($q) {
                        $q->where('tipoVaga', 'idoso');
                    },
                ])
                ->distinct() // ADICIONE ESTA LINHA
                ->get();
        }

        return view('painelDisponibilidade', compact('projetos', 'setores'));
    }
}