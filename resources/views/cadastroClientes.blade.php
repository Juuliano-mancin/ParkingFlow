{{-- resources/views/cadastroClientes.blade.php --}}
@extends('layouts.app')

@section('title', 'Cadastro de Clientes')

@section('content')
<style>
    h1 {
        text-shadow: 1px 4px 5px rgba(51, 51, 51, 0.3);
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
</style>

<div class="container mt-5 p-4 border-custom shadow">
    <h1 class="fs-2 mb-1 underline-title">Cadastro de Cliente</h1>

    {{-- Exibe mensagens de erro --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('clientes.store') }}" method="POST">
        @include('clientes._form', ['buttonText' => 'Cadastrar'])
    </form>
</div>
@endsection