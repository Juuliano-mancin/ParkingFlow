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
                            <tr class="setor-row">
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
                                        <div class="h2 fw-bold text-dark">{{ $totalCarro }}</div>
                                        <div class="text-muted">VAGAS CARRO</div>
                                    </div>
                                </td>

                                {{-- Vagas Moto --}}
                                <td class="px-5 py-4 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="display-6 mb-2">🏍️</span>
                                        <div class="h2 fw-bold text-dark">{{ $totalMoto }}</div>
                                        <div class="text-muted">VAGAS MOTO</div>
                                    </div>
                                </td>

                                {{-- Vagas Preferenciais --}}
                                <td class="px-5 py-4 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="display-6 mb-2">♿</span>
                                        <div class="h2 fw-bold text-dark">{{ $totalDeficiente }}</div>
                                        <div class="text-muted">VAGAS PREFERENCIAIS</div>
                                    </div>
                                </td>

                                {{-- Vagas Idosos --}}
                                <td class="px-5 py-4 text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="display-6 mb-2">👴</span>
                                        <div class="h2 fw-bold text-dark">{{ $totalIdoso }}</div>
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
function atualizarDados() {
    const indicator = document.getElementById('update-indicator');
    if (indicator) {
        indicator.style.opacity = '1';
    }
    
    // Faz requisição para a mesma rota para obter dados atualizados
    fetch('{{ route('painel.disponibilidade') }}?projeto={{ request('projeto') }}')
        .then(response => response.text())
        .then(html => {
            // Cria um elemento temporário para parse do HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Encontra a tabela no HTML retornado
            const novaTabelaContainer = tempDiv.querySelector('#tabela-container');
            
            if (novaTabelaContainer) {
                // Atualiza apenas o conteúdo da tabela de forma instantânea
                document.getElementById('tabela-container').innerHTML = novaTabelaContainer.innerHTML;
            }
            
            // Atualiza o tempo
            updateTime();
            
            // Esconde o indicador após atualização
            if (indicator) {
                setTimeout(() => {
                    indicator.style.opacity = '0';
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar dados:', error);
            // Esconde o indicador em caso de erro
            if (indicator) {
                indicator.style.opacity = '0';
            }
        });
}

// Atualiza a cada 2 segundos
setInterval(atualizarDados, 2000);

// Inicializa o tempo
updateTime();
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