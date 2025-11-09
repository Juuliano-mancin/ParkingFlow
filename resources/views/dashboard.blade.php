@extends('layouts.app') <!--Indica que esta view herda o layout base 'app' localizado na pasta layouts -->

@section('title', 'Dashboard') <!-- Define o valor da seção 'title' como 'Dashboard', geralmente usado para o título da página -->

@section('content') <!-- Inicia a seção 'content' que será injetada no layout principal onde existe @yield('content') -->

    <style>
        .btn {
            font-family: 'Noto Sans', sans-serif;
            background-color: #002B5B;
            color: #F2C200;
            border: none;
            padding: 4rem;
            box-shadow: 1px 5px 6px rgba(0, 0, 0, 0.56);
        }
        .img-fluid {
            filter: invert(1); /* imagem branca por padrão */
            width: 15%;
            transition: filter 0.3s ease;
        }

        /* quando o mouse estiver sobre o link (<a>), muda a imagem */
        a.btn:hover .img-fluid {
            filter: invert(0); /* imagem preta */
        }
        h1 {
            text-shadow: 1px 4px 5px rgba(51, 51, 51, 0.3);
        }
    </style>

    <h1 class="fs-2"> Dashboard Administrativa </h1>
    
    <div class="container">
        <div class="row g-4 mt-4">
            <div class="col-md-6">
                <a href="{{ route('clientes.create') }}"
                class="btn w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 gap-4 text-center">
                    <img src="{{ asset('clientes.png') }}" alt="Novo Cliente" class="img-fluid" style="max-height:120px; object-fit:contain;">
                    <span class="fs-4">Novo Cliente</span>
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('clientes.index') }}"
                class="btn w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 gap-4 text-center">
                    <img src="{{ asset('consulta.png') }}" alt="Consultar Clientes" class="img-fluid" style="max-height:120px; object-fit:contain;">
                    <span class="fs-4">Consultar Clientes</span>
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('estacionamentos.create') }}"
                class="btn w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 gap-4 text-center">
                    <img src="{{ asset('carro.png') }}" alt="Novo Estacionamento" class="img-fluid" style="max-height:120px; object-fit:contain;">
                    <span class="fs-4">Novo Estacionamento</span>
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('vagas.consultar') }}"
                class="btn w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 gap-4 text-center">
                    <img src="{{ asset('visualizacao.png') }}" alt="Visualizar Estacionamento" class="img-fluid" style="max-height:120px; object-fit:contain;">
                    <span class="fs-4">Visualizar Estacionamento</span>
                </a>
            </div>
        </div>
    </div>
@endsection <!-- Finaliza a seção iniciada com @section('content')-->