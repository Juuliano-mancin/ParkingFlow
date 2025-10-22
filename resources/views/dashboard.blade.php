@extends('layouts.app') <!--Indica que esta view herda o layout base 'app' localizado na pasta layouts -->

@section('title', 'Dashboard') <!-- Define o valor da seção 'title' como 'Dashboard', geralmente usado para o título da página -->

@section('content') <!-- Inicia a seção 'content' que será injetada no layout principal onde existe @yield('content') -->

    <h1> Dashboard Administrativa </h1>
    
    <a href="{{ route('clientes.create') }}" class="btn btn-primary mb-3"> Novo Cliente </a>
    <a href="{{ route('clientes.index') }}" class="btn btn-primary mb-3">Consultar Clientes</a>
    <a href="{{ route('estacionamentos.create') }}" class="btn btn-primary mb-3">Novo Estacionamento</a>
    <a href="{{ route('vagas.consultar') }}" class="btn btn-primary mb-3">Visualizar Estacionamento</a>

@endsection <!-- Finaliza a seção iniciada com @section('content')-->