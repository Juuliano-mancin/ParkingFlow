{{-- resources/views/listarClientes.blade.php --}}
@extends('layouts.app')

@section('title', 'Clientes Cadastrados')

@section('content')
<div class="container mt-4">
    <h1>Clientes Cadastrados</h1>

    {{-- Mensagem de sucesso --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabela de clientes --}}
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Razão Social</th>
                <th>Setor</th>
                <th>Segmento</th>
                <th>Representante</th>
                <th>Contato</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clientes as $cliente)
            <tr>
                <td>{{ $cliente->idCliente }}</td>
                <td>{{ $cliente->nomeCliente }}</td>
                <td>{{ $cliente->razaoSocial }}</td>
                <td>{{ ucfirst($cliente->setorAtuacao) }}</td>
                <td>{{ $cliente->segmentoAtuacao }}</td>
                <td>{{ $cliente->representanteCliente }}</td>
                <td>{{ $cliente->contatoCliente }}</td>
                <td>
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