<?php

use Illuminate\Database\Migrations\Migration; /* Importa a classe base para criar migrations no Laravel */
use Illuminate\Database\Schema\Blueprint; /* Importa a classe para definir a estrutura de tabelas no schema */
use Illuminate\Support\Facades\DB; /* Importa a facade DB para operações diretas no banco de dados */
use Illuminate\Support\Facades\Hash; /* Importa a facade Hash para criptografar senhas */

return new class extends Migration /* Cria uma migration anônima que herda da classe Migration para definir alterações no banco de dados */
    {
        public function up(): void /* Método que define as alterações a serem aplicadas no banco de dados ao executar a migration */
        {
            DB::table('users')->insert([
                [
                    'name' => 'Daniel Canivezo',
                    'email' => 'Daniel@parkingflow.adm',
                    'password' => Hash::make('parkingflowadm'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Juliano Mancini',
                    'email' => 'Juliano@parkingflow.adm',
                    'password' => Hash::make('parkingflowadm'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Laura Brianti',
                    'email' => 'Laura@parkingflow.adm',
                    'password' => Hash::make('parkingflowadm'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Monise Jacheta',
                    'email' => 'Monise@parkingflow.adm',
                    'password' => Hash::make('parkingflowadm'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }


        public function down(): void /* Método que reverte as alterações feitas pelo método up() caso a migration seja desfeita */
        {
            DB::table('users')->whereIn('email', [
                'Daniel@parkingflow.adm',
                'Juliano@parkingflow.adm',
                'Laura@parkingflow.adm',
                'Monise@parkingflow.adm',
            ])->delete();
        }
    };