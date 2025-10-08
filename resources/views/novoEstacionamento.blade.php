@extends('layouts.app')

@section('title', 'Novo Estacionamento')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Cadastro de Novo Projeto</h1>

    <div class="card shadow-sm p-4">
        <div class="mb-3">
            <label for="idCliente" class="form-label">Cliente:</label>
            <select name="idCliente" id="idCliente" class="form-select" required>
                <option value="" selected disabled>Selecione um cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->idCliente }}">{{ $cliente->nomeCliente }}</option>
                @endforeach
            </select>
        </div>

        <form id="form-estacionamento" method="POST" action="{{ route('estacionamentos.store') }}" enctype="multipart/form-data" style="display: none;">
            @csrf

            <div class="mb-3">
                <label for="nomeProjeto" class="form-label">Nome do Projeto:</label>
                <input type="text" name="nomeProjeto" id="nomeProjeto" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="planta_baixa" class="form-label">Upload da Planta Baixa:</label>
                <input type="file" name="planta_baixa" id="planta_baixa" class="form-control" accept="image/*" required>
            </div>

            <button type="submit" class="btn btn-primary">Salvar e Continuar</button>
        </form>
    </div>

    <div class="mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Voltar para Dashboard</a>
    </div>
</div>

<script>
    document.getElementById('idCliente').addEventListener('change', function() {
        var form = document.getElementById('form-estacionamento');

        if (this.value !== "") {
            form.style.display = 'block'; 
        } else {
            form.style.display = 'none'; 
        }
    });
</script>
@endsection