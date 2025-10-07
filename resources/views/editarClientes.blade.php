{{-- resources/views/editarClientes.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="container mt-4">
    <h1>Editar Cliente</h1>

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

    <form action="{{ route('clientes.update', $cliente->idCliente) }}" method="POST">
        @method('PUT')
        @include('clientes._form', ['buttonText' => 'Atualizar'])
    </form>
</div>
@endsection