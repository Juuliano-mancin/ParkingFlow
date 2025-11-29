@extends('layouts.app')
 
@section('content')
<div class="container-fluid py-5">
 
    <h1 class="mb-4 text-center text-dark">🅿️ Painel de Disponibilidade</h1>
 
    {{-- Dropdown de seleção de estacionamento --}}
<form method="GET" action="{{ route('painel.disponibilidade') }}" class="mb-5 text-center">
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
 
    {{-- Container da tabela para atualização parcial --}}
<div id="tabela-container">

        @if($setores->count() > 0)
<div class="table-responsive">
<table class="table table-borderless align-middle mb-0">
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
<tr class="setor-row" data-nome-setor="{{ $nomeSetor }}" data-id-setor="{{ $grupo->first()->idSetor ?? '' }}">

                                {{-- Setor --}}
<td class="px-5 py-4">
<div class="d-flex align-items-center">
<div>
<div class="h4 mb-1 fw-bold text-uppercase text-dark">{{ $nomeSetor }}</div>
<small class="text-muted">SETOR</small>
</div>
</div>
</td>
 
                                {{-- Vagas Carro --}}
<td class="px-5 py-4 text-center">
<div class="d-flex flex-column align-items-center">
<span class="display-6 mb-2">🚗</span>
@php
    // Show only free counts on screen. If freeCounts is unavailable yet, show a neutral placeholder (—)
    // We keep data-total attribute for the JS that computes free = total - occupied
    $initialCarro = (isset($freeCounts[$nomeSetor]['carro']) ? $freeCounts[$nomeSetor]['carro'] : '');
@endphp
<div class="h2 fw-bold text-dark total-count" data-tipo="carro" data-setor="{{ $nomeSetor }}" data-total="{{ $totalCarro }}">{{ $initialCarro }}</div>
<!-- indicador pequeno removido; o número grande mostra vagas livres -->
<div class="text-muted">VAGAS CARRO</div>
</div>
</td>
 
                                {{-- Vagas Moto --}}
<td class="px-5 py-4 text-center">
<div class="d-flex flex-column align-items-center">
<span class="display-6 mb-2">🏍️</span>
@php $initialMoto = (isset($freeCounts[$nomeSetor]['moto']) ? $freeCounts[$nomeSetor]['moto'] : ' '); @endphp
<div class="h2 fw-bold text-dark total-count" data-tipo="moto" data-setor="{{ $nomeSetor }}" data-total="{{ $totalMoto }}">{{ $initialMoto }}</div>
<!-- indicador pequeno removido; o número grande mostra vagas livres -->
<div class="text-muted">VAGAS MOTO</div>
</div>
</td>
 
                                {{-- Vagas Preferenciais --}}
<td class="px-5 py-4 text-center">
<div class="d-flex flex-column align-items-center">
<span class="display-6 mb-2">♿</span>
@php $initialDeficiente = (isset($freeCounts[$nomeSetor]['deficiente']) ? $freeCounts[$nomeSetor]['deficiente'] : ' '); @endphp
<div class="h2 fw-bold text-dark total-count" data-tipo="deficiente" data-setor="{{ $nomeSetor }}" data-total="{{ $totalDeficiente }}">{{ $initialDeficiente }}</div>
<!-- indicador pequeno removido; o número grande mostra vagas livres -->
<div class="text-muted">VAGAS PREFERENCIAIS</div>
</div>
</td>
 
                                {{-- Vagas Idosos --}}
<td class="px-5 py-4 text-center">
<div class="d-flex flex-column align-items-center">
<span class="display-6 mb-2">👴</span>
@php $initialIdoso = (isset($freeCounts[$nomeSetor]['idoso']) ? $freeCounts[$nomeSetor]['idoso'] : ' '); @endphp
<div class="h2 fw-bold text-dark total-count" data-tipo="idoso" data-setor="{{ $nomeSetor }}" data-total="{{ $totalIdoso }}">{{ $initialIdoso }}</div>
<!-- indicador pequeno removido; o número grande mostra vagas livres -->
<div class="text-muted">VAGAS IDOSOS</div>
</div>
</td>
</tr>

                            {{-- Linha separadora --}}

                            @if(!$loop->last)
<tr>
<td colspan="5" class="px-0">
<div class="border-bottom border-light"></div>
</td>
</tr>

                            @endif

                        @endforeach
</tbody>
</table>
</div>
 
            <div class="mt-5 text-center">
<h5 class="text-muted">🕒 Atualizado às <span id="current-time" class="text-dark">{{ now()->format('H:i') }}</span></h5>
<small class="text-muted" id="update-indicator">● Atualizando...</small>
</div>

        @else
<p class="text-muted text-center">Selecione um estacionamento para visualizar os setores.</p>

        @endif
</div>
 
</div>
 
{{-- Script para auto-atualização --}}
<script>

function updateTime() {

    const now = new Date();

    const hours = now.getHours().toString().padStart(2, '0');

    const minutes = now.getMinutes().toString().padStart(2, '0');

    const timeString = `${hours}:${minutes}`;

    document.getElementById('current-time').textContent = timeString;

}
 
// Função para buscar dados atualizados via API

// Simplified updater: only update the time and counters to avoid replacing the whole table
// which can cause visual flicker. Keep updates imperceptible by using subtle transitions.
async function atualizarDados() {
    const indicator = document.getElementById('update-indicator');
    if (indicator) indicator.style.opacity = '1';

    // Update the displayed time immediately
    updateTime();

    // Update counters from APIs (no DOM replacement)
    try {
        await fetchSensorStatus();
    } catch (err) {
        console.error('Erro ao atualizar contadores:', err);
    }

    // Small fade-out for the 'updating' indicator
    if (indicator) {
        setTimeout(() => {
            indicator.style.opacity = '0';
        }, 450);
    }
}
 
// Atualiza a cada 2 segundos

// Keep frequent updates but smoother: every 2s
setInterval(atualizarDados, 6000);
 
// Inicializa o tempo

updateTime();

// ===== Funções para consultar sensores e vagas-inteligentes e atualizar contadores =====

async function fetchSensorStatus(){
    try{
        // fetch both endpoints in parallel
        const [sensoresRes, vagasIntRes] = await Promise.all([
            fetch('/api/sensores'),
            fetch('/api/vagas-inteligentes')
        ]);

        const sensores = sensoresRes.ok ? await sensoresRes.json() : [];
        const vagasInt = vagasIntRes.ok ? await vagasIntRes.json() : [];

        // Build a map: setor -> tipo -> occupiedCount
        const mapa = {};

        vagasInt.forEach(item => {
            const vaga = item.vaga || {};
            const setor = item.setor || (vaga && vaga.setor) || null;
            const sensor = item.sensor || {};

            if(!setor || !vaga) return;

            const nomeSetorRaw = setor.nomeSetor || setor.nome || '';
            const nomeSetor = String(nomeSetorRaw).trim().toLowerCase();
            const tipo = String((vaga.tipoVaga || vaga.tipo || 'carro')).trim().toLowerCase();

            if(!mapa[nomeSetor]) mapa[nomeSetor] = {carro:0,moto:0,deficiente:0,idoso:0};

            // Determine if sensor is occupied. Try multiple possible fields
            const occupied = sensor.hasOwnProperty('statusManual') ? !!sensor.statusManual
                : sensor.hasOwnProperty('status') ? !!sensor.status
                : sensor.hasOwnProperty('ocupado') ? !!sensor.ocupado : false;

            if(occupied){
                if(!mapa[nomeSetor][tipo]) mapa[nomeSetor][tipo] = 0; // safety
                mapa[nomeSetor][tipo]++;
            }
        });

        // small indicators removed — the main counters (.total-count) will show vagas livres

        // Update the large counters to show number of free spots (free = total - occupied)
        document.querySelectorAll('.total-count').forEach(el => {
            const setorRaw = el.dataset.setor || '';
            const setor = String(setorRaw).trim().toLowerCase();
            const tipo = String(el.dataset.tipo || '').trim().toLowerCase();
            const totalAttr = el.dataset.total;
            const total = Number.isFinite(+totalAttr) ? parseInt(totalAttr, 10) : 0;
            const occupiedCount = mapa[setor] && mapa[setor][tipo] ? mapa[setor][tipo] : 0;
            const free = Math.max(0, total - occupiedCount);

            // animate subtle change
            const previous = el.textContent;
            // Only update the DOM if value changed (prevents reflows)
            if (String(previous) !== String(free)) {
                // transition class for a quick scale effect
                el.classList.add('count-updated');
                // smooth swap without clearing layout
                el.textContent = `${free}`;
                // remove the class after the CSS animation
                setTimeout(() => el.classList.remove('count-updated'), 350);
            }
            // style accordingly
            if (free > 0) {
                el.style.color = '#28a745';
                el.style.fontWeight = '700';
            } else {
                el.style.color = '#6c757d';
                el.style.fontWeight = '500';
            }
        });

    }catch(err){
        console.error('Erro ao carregar sensores/vagas-inteligentes:', err);
    }
}

// Run once on initial load
fetchSensorStatus();
</script>
 
<style>

body {

    background: white;

    min-height: 100vh;

    margin: 0;

    padding: 0;

}
 
.table {

    background: transparent;

    font-size: 1.1rem;

}
 
.setor-row {

    background: rgba(0, 0, 0, 0.02);

    border-radius: 20px;

    margin-bottom: 1rem;

    border: 1px solid rgba(0, 0, 0, 0.05);

}
 
.setor-row:hover {

    background: rgba(0, 0, 0, 0.05);

}
 
.table-responsive {

    border-radius: 25px;

    padding: 1rem;

}
 
.container-fluid {

    max-width: 1800px;

}
 
.display-6 {

    font-size: 3rem;

}
 
.h2 {

    font-size: 3.5rem;

}
 
.h4 {

    font-size: 2rem;

}
 
.form-select {

    background: white;

    border: 2px solid #e9ecef;

    color: #495057;

    border-radius: 10px;

    padding: 0.75rem 1.5rem;

    font-size: 1.1rem;

}
 
.form-select:focus {

    background: white;

    border-color: #007bff;

    color: #495057;

    box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);

}

/* Smooth transitions for counter updates */
.total-count{
    transition: color 200ms ease, transform 220ms ease, opacity 180ms ease;
    will-change: transform, color, opacity;
}

.total-count.count-updated{
    transform: scale(1.06);
    opacity: 0.95;
}

/* subtle update indicator */
#update-indicator{
    transition: opacity 320ms ease;
    opacity: 0; /* hidden by default; toggled to visible while updating */
}

/* placeholder dash for when initial free count is unavailable */
.total-count:empty::before{
    /* keep placeholder styling consistent */
    color: #6c757d;
}
 
.form-select option {

    background: white;

    color: #495057;

}
 
.border-light {

    border-color: #f8f9fa !important;

}
 
/* Indicador discreto de atualização */

#update-indicator {

    opacity: 0;

    transition: opacity 0.3s ease;

    font-size: 0.8rem;

    color: #6c757d;

}
 
#update-indicator::before {

    content: '●';

    color: #28a745;

    margin-right: 5px;

}
</style>

@endsection
 