<?php

namespace App\Http\Controllers\Auth; /* Define o namespace do controlador de autenticação */

use App\Http\Controllers\Controller; /* Importa a classe base Controller do Laravel */
use Illuminate\Http\Request; /* Importa a classe Request para lidar com requisições HTTP */
use Illuminate\Support\Facades\Auth;  /* Importa a facade Auth para gerenciar autenticação de usuários */

class LoginController extends Controller  /* Define a classe LoginController que herda funcionalidades da classe base Controller */
    {
        public function showLoginForm() /* Declara o método responsável por exibir o formulário de login */
        {
            return view('welcome');
        }

        
        public function login(Request $request) /* Declara o método que processa os dados enviados pelo formulário de login */
        {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return redirect()->intended('/dashboard');
            }

            return back()->withErrors([
                'email' => 'As credenciais não conferem',
            ])->onlyInput('email');
        }

    
        public function logout(Request $request) /* Método que encerra a sessão do usuário e realiza o logout */
        {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        }
    }