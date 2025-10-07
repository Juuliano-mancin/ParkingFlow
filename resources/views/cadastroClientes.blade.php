{{-- resources/views/cadastroClientes.blade.php --}}
@extends('layouts.app')

@section('title', 'Cadastro de Clientes')

@section('content')
<div class="container mt-4">
    <h1>Cadastro de Cliente</h1>

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