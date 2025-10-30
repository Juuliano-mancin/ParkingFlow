@extends('layouts.app')

@section('title', 'Novo Projeto')

@section('content')
<style>
    .card {
        border-radius: 0;
        width: 80%;
    }
    .border-custom {
        border: 1px solid #4B4B4B;
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
    button[type="submit"] {
        color: #F2C200;
        background-color: #002B5B;
    }
    button[type="submit"]:hover {
        background-color: #0056b3;
    }
    label {
        text-shadow: 1px 2px 3px rgba(0,0,0,0.25);
        color: #4B4B4B;
    }
    input, select {
        box-shadow: 3px 3px 3px rgba(184, 184, 184, 0.25);
    }
</style>
<div class="container py-5">
    <div class="card shadow border-custom p-5 w-80 justify-content-center mx-auto">
        <form id="form-estacionamento" method="POST" action="{{ route('estacionamentos.store') }}" enctype="multipart/form-data">
            @csrf
            <h1 class="fs-2 mb-1 underline-title">Cadastro de Novo Projeto</h1>

            <!-- Seleção do Cliente -->
            <div class="mb-4">
                <label for="idCliente" class="form-label">Cliente:</label>
                <select name="idCliente" id="idCliente" class="form-select" required>
                    <option value="" selected disabled>Selecione um cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->idCliente }}">{{ $cliente->nomeCliente }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nome do Projeto -->
            <div class="mb-4">
                <label for="nomeProjeto" class="form-label">Nome do Projeto:</label>
                <input type="text" name="nomeProjeto" id="nomeProjeto" class="form-control" required>
            </div>

            <!-- Upload da Planta Baixa -->
            <div class="mb-4">
                <label for="planta_baixa" class="form-label">Upload da Planta Baixa:</label>
                <input type="file" name="planta_baixa" id="planta_baixa" class="form-control" accept="image/*" required>
            </div>

            <div class="underline-title"></div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary shadow">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- Script opcional: mostrar campos adicionais se necessário -->
<script>
    // Exemplo: você pode ativar/desativar campos extras com base na seleção do cliente
    document.getElementById('idCliente').addEventListener('change', function() {
        var selected = this.value;
        // Aqui você pode mostrar outros campos dinamicamente se quiser
        // Mas o select já está dentro do form, então idCliente será enviado
    });
</script>
@endsection