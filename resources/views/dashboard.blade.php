@extends('layouts.app') <!--Indica que esta view herda o layout base 'app' localizado na pasta layouts -->

@section('title', 'Dashboard') <!-- Define o valor da seção 'title' como 'Dashboard', geralmente usado para o título da página -->

@section('content') <!-- Inicia a seção 'content' que será injetada no layout principal onde existe @yield('content') -->
    <h1> Aqui vai o conteudo da Dashboard Administrativa </h1> <!-- Conteúdo específico da página -->
@endsection <!-- Finaliza a seção iniciada com @section('content')-->