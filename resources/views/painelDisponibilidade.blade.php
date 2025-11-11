@extends('layouts.app')

@section('content')
<div class="container text-center py-4">

    <h1 class="mb-4">🅿️ Painel de Disponibilidade</h1>

    {{-- Dropdown de seleção de estacionamento --}}
    <form method="GET" action="{{ route('painel.disponibilidade') }}" class="mb-4">
        <select name="projeto" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
            <option value="">Selecione o estacionamento</option>
            @foreach($projetos as $projeto)
                <option value="{{ $projeto->idProjeto }}" 
                    {{ request('projeto') == $projeto->idProjeto ? 'selected' : '' }}>
                    {{ $projeto->nomeProjeto }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- Exibição dos setores --}}
    @if($setores->count() > 0)
        <div class="table-responsive">
            <table class="table table-dark table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Setor</th>
                        <th>🚗 Carros</th>
                        <th>🏍️ Motos</th>
                        <th>♿ Deficientes</th>
                        <th>👴 Idosos</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Agrupa por nome do setor e soma as vagas
                        $setoresAgrupados = $setores->groupBy('nomeSetor');
                    @endphp
                    
                    @foreach($setoresAgrupados as $nomeSetor => $grupo)
                        @php
                            $totalCarro = $grupo->sum('vagas_carro');
                            $totalMoto = $grupo->sum('vagas_moto');
                            $totalDeficiente = $grupo->sum('vagas_deficiente');
                            $totalIdoso = $grupo->sum('vagas_idoso');
                        @endphp
                        <tr>
                            <td><strong>{{ $nomeSetor }}</strong></td>
                            <td>{{ $totalCarro }}</td>
                            <td>{{ $totalMoto }}</td>
                            <td>{{ $totalDeficiente }}</td>
                            <td>{{ $totalIdoso }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-end">
            <h5>🕒 Atualizado às <span id="current-time">{{ now()->format('H:i:s') }}</span></h5>
        </div>
    @else
        <p class="text-muted">Selecione um estacionamento para visualizar os setores.</p>
    @endif
</div>

{{-- Script para auto-atualização a cada segundo --}}
<script>
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('pt-BR');
    document.getElementById('current-time').textContent = timeString;
}

// Atualiza o tempo a cada segundo
setInterval(updateTime, 1000);

// Recarrega a página a cada 5 segundos para atualizar os dados
setTimeout(function() {
    window.location.reload();
}, 5000);
</script>
@endsection