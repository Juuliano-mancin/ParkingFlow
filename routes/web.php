<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController; /* Importa o LoginController para ser usado nas rotas */

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login'); /* Rota GET para exibir o formulário de login */
Route::post('/login', [LoginController::class, 'login'])->name('login.post'); /* Rota POST para processar o login */
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); /* Rota POST para realizar o logout do usuário */

Route::middleware('auth')->group(function () /* Agrupa rotas que só podem ser acessadas por usuários autenticados */
    { 
        /* Retorna a view da dashboard administrativa e Nomeia a rota como 'dashboard' para facilitar referências */
        Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
        
    });    