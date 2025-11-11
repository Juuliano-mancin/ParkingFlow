<?php

// Importações de classes que serão usadas neste arquivo
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstacionamentoController;
use App\Http\Controllers\PainelDisponibilidadeController;
use App\Http\Controllers\VagaController;
use App\Http\Controllers\VagaInteligenteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui é onde você registra as rotas web para sua aplicação.
| Estas rotas são carregadas pelo RouteServiceProvider dentro de um grupo
| que contém o middleware "web". Agora crie algo incrível!
|
*/

// Rota padrão para a página inicial
Route::get('/', function () {
    return view('welcome');
});

// Rotas para gerenciamento de estacionamentos
// GET /estacionamento/novo - Exibe o formulário para criar um novo estacionamento
Route::get('/estacionamento/novo', [EstacionamentoController::class, 'create'])->name('estacionamento.create');

// POST /estacionamento - Processa o formulário e salva o novo estacionamento
Route::post('/estacionamento', [EstacionamentoController::class, 'store'])->name('estacionamento.store');

// Rota para o painel de disponibilidade
// GET /painel-disponibilidade - Exibe o painel de disponibilidade de vagas
Route::get('/painel-disponibilidade', [PainelDisponibilidadeController::class, 'index'])->name('painel.disponibilidade');

// Rotas para gerenciamento de vagas
// GET /vagas/consultar - Exibe a página de consulta de vagas
Route::get('/vagas/consultar', [VagaController::class, 'consultar'])->name('vagas.consultar');

// GET /vagas/create/{idProjeto} - Exibe o formulário para criar uma nova vaga
Route::get('/vagas/create/{idProjeto}', [VagaController::class, 'create'])->name('vagas.create');

// GET /vagas/visualizar/{idProjeto} - Exibe a página de visualização de vagas
Route::get('/vagas/visualizar/{idProjeto}', [VagaController::class, 'visualizar'])->name('vagas.visualizar');

// GET /vagas/listar/{idProjeto} - Retorna a lista de vagas de um projeto (para AJAX)
Route::get('/vagas/listar/{idProjeto}', [VagaController::class, 'listar'])->name('vagas.listar');

// Rotas para vagas inteligentes
// GET /vagas-inteligentes - Exibe a página principal de vagas inteligentes
Route::get('/vagas-inteligentes', [VagaInteligenteController::class, 'index'])->name('vagas.inteligentes');

// GET /vagas-inteligentes/associar - Exibe a página para associar sensores a vagas
Route::get('/vagas-inteligentes/associar', [VagaInteligenteController::class, 'associar'])->name('vagas.inteligentes.associar');

// GET /vagas-inteligentes/dados - Retorna os dados das vagas inteligentes (para AJAX)
Route::get('/vagas-inteligentes/dados', [VagaInteligenteController::class, 'getVagasInteligentes'])->name('vagas.inteligentes.dados');

// Nova rota para o painel de vagas inteligentes
// GET /painel-vagas-inteligentes - Exibe o painel de monitoramento em tempo real
Route::get('/painel-vagas-inteligentes', [VagaInteligenteController::class, 'painelVagasInteligentes'])->name('painel.vagas.inteligentes');