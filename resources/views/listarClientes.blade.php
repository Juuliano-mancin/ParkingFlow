{{-- resources/views/listarClientes.blade.php --}}
@extends('layouts.app')

@section('title', 'Clientes Cadastrados')

@section('content')
<style>
    .btn.btn-sm.btn-warning{
        color: #F2C200;
        background-color: #002B5B;
        border: 1px solid #0056b3;
    }

    th, td {
        border: 1px solid #4B4B4B; /* borda das células */
        padding: 10px;
    }
    tbody tr td {
        background-color: #F8F8F8 !important; /* amarelo */
    }
    .underline-title {
        position: relative;
        padding-bottom: 0.6rem;
    }
    .underline-title::after {
        content: "";
        display: block;
        width: 100%;          
        height: 1px;           
        background: #F2C200;    
        border-radius: 2px;
        margin: 0.8rem 0;  
    }

</style>
<div class="container mt-4">
    <h1 class="fs-2 mb-2 underline-title">Clientes Cadastrados</h1>

    {{-- Mensagem de sucesso --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabela de clientes --}}
    <table class="table border border-warning">
        <thead class="bg-white">
            <tr>
                <th class="p-3">ID</th>
                <th class="p-3">Nome</th>
                <th class="p-3">Razão Social</th>
                <th class="p-3">Setor</th>
                <th class="p-3">Segmento</th>
                <th class="p-3">Representante</th>
                <th class="p-3">Contato</th>
                <th class="p-3">Ações</th>
            </tr>
        </thead>
        <tbody class="bg-warning">
            @foreach ($clientes as $cliente)
            <tr>
                <td class="p-3">{{ $cliente->idCliente }}</td>
                <td class="p-3">{{ $cliente->nomeCliente }}</td>
                <td class="p-3">{{ $cliente->razaoSocial }}</td>
                <td class="p-3">{{ ucfirst($cliente->setorAtuacao) }}</td>
                <td class="p-3">{{ $cliente->segmentoAtuacao }}</td>
                <td class="p-3">{{ $cliente->representanteCliente }}</td>
                <td class="p-3">{{ $cliente->contatoCliente }}</td>
                <td class="p-3">
                    {{-- Botão Editar --}}
                    <a href="{{ route('clientes.edit', $cliente->idCliente) }}" class="btn btn-sm btn-warning">
                        Editar
                    </a>

                    {{-- Botão Excluir --}}
                    <form action="{{ route('clientes.destroy', $cliente->idCliente) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Tem certeza que deseja excluir este cliente?');">
                            Excluir
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection