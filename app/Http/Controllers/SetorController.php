<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setor;
use App\Models\Projeto;

class SetorController extends Controller
{

    public function create($idProjeto)
        {
            $projeto = Projeto::findOrFail($idProjeto);

            // Garante que o caminho esteja no formato correto para a view
            $caminhoPublico = 'storage/' . ltrim($projeto->caminhoPlantaEstacionamento, '/');

            return view('novoSetor', [
                'projeto' => $projeto,
                'caminhoPublico' => $caminhoPublico,
            ]);
        }


    public function store(Request $request)
        {
            $request->validate
                ([
                    'idProjeto' => 'required|exists:tb_projetos,idProjeto',
                    'nomeSetor' => 'required|string|max:255',
                    'corSetor' => 'required|string|max:7', // Ex: #FF0000
                    'setorCoordenadaX' => 'nullable|integer',
                    'setorCoordenadaY' => 'nullable|integer',
                ]);

            $setor = Setor::create
                ([
                    'idProjeto' => $request->idProjeto,
                    'nomeSetor' => $request->nomeSetor,
                    'corSetor' => $request->corSetor,
                    'setorCoordenadaX' => $request->setorCoordenadaX,
                    'setorCoordenadaY' => $request->setorCoordenadaY,
                ]);

            return redirect()->route('vagas.create', ['idProjeto' => $request->idProjeto])->with('success', 'Setor cadastrado com sucesso!');
        }
}