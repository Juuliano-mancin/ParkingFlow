<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController; /* Importa o LoginController para ser usado nas rotas */
use App\Http\Controllers\ClienteController; /* Importa o ClienteController para ser usado nas rotas */
use App\Http\Controllers\EstacionamentoController; /* Importa o EstacionamentoController para ser usado nas rotas */
use App\Http\Controllers\SetorController; /* Importa o SetorController para ser usado nas rotas */
use App\Http\Controllers\VagaController; /* Importa o VagaController para ser usado nas rotas */



Route::get('/', [LoginController::class, 'showLoginForm'])->name('login'); /* Rota GET para exibir o formulário de login */
Route::post('/login', [LoginController::class, 'login'])->name('login.post'); /* Rota POST para processar o login */
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); /* Rota POST para realizar o logout do usuário */

Route::middleware('auth')->group(function () /* Agrupa rotas que só podem ser acessadas por usuários autenticados */
    { 
        /* Retorna a view da dashboard administrativa e Nomeia a rota como 'dashboard' para facilitar referências */
        Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');

        /* Cadastros, edição e exclusão de clientes*/
        Route::get('/clientes/novo', [ClienteController::class, 'create'])->name('clientes.create'); /* Rota GET para formulário para criar um novo cliente, nomeada como 'clientes.create' */
        Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store'); /* Rota POST para salvar um novo cliente, nomeada como 'clientes.store' */
        Route::get('/clientes/{id}/editar', [ClienteController::class, 'edit'])->name('clientes.edit'); /* Rota GET para formulário de edição de cliente, nomeada como */
        Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update'); /* Rota PUT para atualizar um cliente, nomeada como 'clientes.update' */
        Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy'); /* Rota DELETE para excluir um cliente, nomeada como 'clientes.destroy'*/
        Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index'); /* Rota GET para listar todos os clientes, nomeada como 'clientes.index' */
        Route::get('/estacionamentos/novo', [EstacionamentoController::class, 'create'])->name('estacionamentos.create'); /* Rota GET para formulário para criar um novo estacionamento, nomeada como 'estacionamentos.create' */
        Route::post('/estacionamentos', [EstacionamentoController::class, 'store'])->name('estacionamentos.store'); /* Rota POST para salvar um novo estacionamento, nomeada*/
        Route::get('/setores/novo/{idProjeto}', [SetorController::class, 'create'])->name('setores.create'); /* Rota GET para formulário para criar um novo setor, nomeada como 'setores.create' */
        Route::post('/setores', [SetorController::class, 'store'])->name('setores.store'); /* Rota POST para salvar um novo setor, nomeada como 'setores.store' */
        Route::get('/vagas/nova/{idSetor}', [VagaController::class, 'create'])->name('vagas.nova'); /* Rota GET para formulário para criar uma nova vaga, nomeada como 'vagas.nova' */
        Route::post('/vagas', [VagaController::class, 'store'])->name('vagas.store'); /* Rota POST para salvar uma nova vaga, nomeada como 'vagas.store' */
        Route::get('/api/setores/{idProjeto}', [SetorController::class, 'getSetores']); /* Rota GET para obter setores de um projeto específico via API */
        Route::prefix('vagas')->group(function () 
            {
                // Vagas - rotas canônicas (sem duplicação)
                Route::get('/create/{idProjeto}', [VagaController::class, 'create'])->name('vagas.create');
                Route::post('/vagas', [VagaController::class, 'store'])->name('vagas.store');
                Route::get('/listar/{idProjeto}', [VagaController::class, 'listar'])->name('vagas.index');
            });
            
            Route::get('/vagas/visualizar/{idProjeto}', [VagaController::class, 'visualizar'])->name('vagas.visualizar'); // visualizar (página read-only)            
            Route::get('/vagas/listar/{idProjeto}', [VagaController::class, 'listar'])->name('vagas.listar'); // endpoint que já existe para JSON: listar
            Route::get('/vagas/consultar', [VagaController::class, 'consultar'])->name('vagas.consultar'); // Página de consulta / seleção de estacionamento (leva à view consultarEstacionamento)
            Route::get('/painel-disponibilidade', [App\Http\Controllers\PainelDisponibilidadeController::class, 'index'])->name('painel.disponibilidade'); // Painel de disponibilidade de setores

    });