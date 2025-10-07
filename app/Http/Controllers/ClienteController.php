<?php

namespace App\Http\Controllers; /* Define o namespace para o controlador, organizando-o dentro do diretório App\Http\Controllers */

use Illuminate\Http\Request; /* Importa a classe Request do Laravel para manipular dados de requisições HTTP */
use Illuminate\Support\Facades\DB; /* Importa a facade DB para interagir com o banco de dados */

class ClienteController extends Controller{   

        public function create() /* Método que exibe o formulário para criação de um novo registro, neste caso, um cliente */
            {
                
                return view('cadastroClientes');
            }

        public function store(Request $request) /* Método que processa o formulário de criação de um novo cliente */
            {
                $data = $request->validate([
                    'nomeCliente' => 'required|string|max:255',
                    'razaoSocial' => 'required|string|max:255',
                    'setorAtuacao' => 'required|in:comercial,industrial,residencial,outros',
                    'segmentoAtuacao' => 'required|string|max:255',
                    'representanteCliente' => 'required|string|max:255',
                    'emailCliente' => 'nullable|email|max:255',
                    'contatoCliente' => 'nullable|numeric',
                    'cepCliente' => 'nullable|string|max:20',
                    'logradouroCliente' => 'nullable|string|max:255',
                    'numeroCliente' => 'nullable|string|max:20',
                    'complementoCliente' => 'nullable|string|max:255',
                    'cidadeCliente' => 'nullable|string|max:255',
                    'ufCliente' => 'nullable|string|max:2',
                ]);

                DB::table('tb_clientes')->insert($data);

                return redirect()->route('clientes.create')->with('success', 'Cliente cadastrado com sucesso!');
            }

            public function edit($id) /* Método que exibe o formulário para edição de um cliente existente */
                {
                    $cliente = DB::table('tb_clientes')->where('idCliente', $id)->first();

                    return view('editarClientes', compact('cliente'));
                }

            public function update(Request $request, $id) /* Método que processa o formulário de edição de um cliente existente */
                {
                    $data = $request->validate([
                        'nomeCliente' => 'required|string|max:255',
                        'razaoSocial' => 'required|string|max:255',
                        'setorAtuacao' => 'required|in:comercial,industrial,residencial,outros',
                        'segmentoAtuacao' => 'required|string|max:255',
                        'representanteCliente' => 'required|string|max:255',
                        'emailCliente' => 'nullable|email|max:255',
                        'contatoCliente' => 'nullable|numeric',
                        'cepCliente' => 'nullable|string|max:20',
                        'logradouroCliente' => 'nullable|string|max:255',
                        'numeroCliente' => 'nullable|string|max:20',
                        'complementoCliente' => 'nullable|string|max:255',
                        'cidadeCliente' => 'nullable|string|max:255',
                        'ufCliente' => 'nullable|string|max:2',
                    ]);

                    DB::table('tb_clientes')->where('idCliente', $id)->update($data);

                    return redirect()->route('clientes.edit', $id)->with('success', 'Cliente atualizado com sucesso!');
                }

            public function destroy($id) /* Método que exclui um cliente existente */
                {
                    DB::table('tb_clientes')->where('idCliente', $id)->delete();

                    return redirect()->route('dashboard')->with('success', 'Cliente excluído com sucesso!');
                }

            public function index() /* Método que lista todos os clientes */
                {
                    $clientes = DB::table('tb_clientes')->get(); // busca todos os clientes
                    return view('listarClientes', compact('clientes'));
                }
}