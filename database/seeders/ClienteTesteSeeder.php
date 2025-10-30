<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteTesteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_clientes')->insert([
            'nomeCliente' => 'Fatec Itapira',
            'razaoSocial' => 'Fatec Ogari de Castro Pacheco',
            'setorAtuacao' => 'outros',
            'segmentoAtuacao' => 'Educação',
            'representanteCliente' => 'Junior',
            'emailCliente' => 'Junior@Fatec.com',
            'contatoCliente' => 19998971234,
            'cepCliente' => '13976100',
            'logradouroCliente' => 'Rua Fatec',
            'numeroCliente' => '100',
            'complementoCliente' => 'Sem Complemento',
            'cidadeCliente' => 'Itapira',
            'ufCliente' => 'SP',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}