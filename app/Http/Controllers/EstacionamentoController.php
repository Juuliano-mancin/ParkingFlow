<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Projeto;

class EstacionamentoController extends Controller
{
    public function create()
        {
            $clientes = Cliente::all(); /* Busca todos os clientes cadastrados no banco de dados */
            return view('novoEstacionamento', compact('clientes')); /* Retorna a view 'novoEstacionamento' passando a lista de clientes para o formulário */
        }

    public function store(Request $request)
        {

            $request->validate
                ([
                    'nomeProjeto' => 'required|string|max:255',
                    'planta_baixa' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
                ]);

                if ($request->hasFile('planta_baixa')) 
                    {
                        $arquivo = $request->file('planta_baixa');
                        $nomeArquivo = time() . '_' . $arquivo->getClientOriginalName();

                        $caminho = $arquivo->storeAs('plantas', $nomeArquivo, 'public');
                    } 
                else 
                    {
                        $nomeArquivo = null;
                        $caminho = null;
                    }

            $projeto = Projeto::create
                ([
                    'idCliente' => $request->idCliente,
                    'nomeProjeto' => $request->nomeProjeto,
                    'plantaEstacionamento' => $nomeArquivo,
                    'caminhoPlantaEstacionamento' => $caminho,
                ]);

            return redirect()->route('setores.create', ['idProjeto' => $projeto->idProjeto])->with('success', 'Projeto cadastrado com sucesso!');
        }
}