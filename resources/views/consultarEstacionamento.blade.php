@extends('layouts.app')

@section('title', 'Consultar Estacionamento')

@section('content')
<div class="container-fluid p-0 vh-100">
    <div class="row g-0 h-100">

        <!-- === VIEWPORT PRINCIPAL - COLUNA 9 === -->
        <div class="col-9 position-relative" style="background-color:#f0f0f0; overflow:hidden;">
            
            <!-- Indicador de Zoom -->
            <div class="zoom-indicator" style="position:absolute; top:15px; right:15px; z-index:1001; background:rgba(0,0,0,0.7); color:white; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:500; backdrop-filter:blur(10px);">
                <span id="zoomLevel">100%</span>
            </div>

            <!-- CONTROLES DE VISUALIZAÇÃO -->
            <div class="position-absolute top-0 start-0 m-3 z-3">
                <div class="btn-group shadow-sm">
                    <button id="toggleSetores" class="btn btn-primary active">
                        <i class="fas fa-layer-group me-2"></i> Setores
                    </button>
                    <button id="toggleVagas" class="btn btn-success active">
                        <i class="fas fa-car me-2"></i> Vagas
                    </button>
                </div>
            </div>

            <!-- CONTROLES DE NAVEGAÇÃO -->
            <div class="position-absolute bottom-0 start-50 translate-middle-x mb-3 z-3">
                <div class="btn-group shadow-sm">
                    <button id="btnZoomOut" class="btn btn-outline-secondary" title="Zoom Out">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <button id="btnZoomReset" class="btn btn-outline-primary" title="Reset Zoom">
                        100%
                    </button>
                    <button id="btnZoomIn" class="btn btn-outline-secondary" title="Zoom In">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>
            </div>

            <div id="viewer" 
                 style="width:100%; height:100%; cursor:crosshair; user-select:none; background-repeat:no-repeat; background-position:center center; background-size:contain; position:relative;">
                
                <!-- Camada de Setores (fundo) -->
                <div id="setores-layer" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
                
                <!-- Camada de Vagas (sobreposição) -->
                <div id="vagas-layer" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
            </div>
        </div>

        <!-- === PAINEL DE CONTROLE - COLUNA 3 === -->
        <div class="col-3 d-flex flex-column" style="background-color: #f8f9fa; border-left: 1px solid #dee2e6;">
            <div class="control-panel h-100 d-flex flex-column p-4 overflow-auto">
                
                <!-- Cabeçalho -->
                <div class="text-center mb-4">
                    <h4 class="text-primary mb-2">Consulta do Estacionamento</h4>
                    <p class="text-muted small">Visualize setores e vagas cadastradas</p>
                </div>

                <!-- === SELEÇÃO DE PROJETO === -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-3">
                            <i class="fas fa-project-diagram me-2"></i>Selecionar Projeto
                        </h5>
                        <label for="selectProjeto" class="form-label small text-muted">Projeto</label>
                        <select id="selectProjeto" class="form-select border-primary">
                            @foreach($projetos as $p)
                                <option value="{{ $p->idProjeto }}"
                                        data-caminho="{{ $p->caminhoPlantaEstacionamento }}"
                                        {{ (isset($projeto) && $projeto->idProjeto === $p->idProjeto) ? 'selected' : '' }}>
                                    {{ $p->nomeProjeto }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- === LEGENDA DOS SETORES === -->
                <div class="card border-0 shadow-sm mb-4" id="legendaSetoresCard" style="display:none;">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-3">
                            <i class="fas fa-layer-group me-2"></i>Legenda dos Setores
                        </h5>
                        <div id="legendaSetores" class="d-flex flex-wrap justify-content-center gap-2"></div>
                    </div>
                </div>

                <!-- === LEGENDA DOS TIPOS DE VAGA === -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-3">
                            <i class="fas fa-tags me-2"></i>Tipos de Vaga
                        </h5>
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center p-2 border rounded bg-light">
                                <div class="vaga-indicador me-3" style="width:20px; height:20px; background-color:rgba(0,123,255,0.6); border-radius:4px;"></div>
                                <div class="vaga-label flex-grow-1">Carro</div>
                                <i class="fas fa-car text-primary"></i>
                            </div>
                            <div class="d-flex align-items-center p-2 border rounded bg-light">
                                <div class="vaga-indicador me-3" style="width:20px; height:20px; background-color:rgba(40,167,69,0.6); border-radius:4px;"></div>
                                <div class="vaga-label flex-grow-1">Moto</div>
                                <i class="fas fa-motorcycle text-success"></i>
                            </div>
                            <div class="d-flex align-items-center p-2 border rounded bg-light">
                                <div class="vaga-indicador me-3" style="width:20px; height:20px; background-color:rgba(255,193,7,0.6); border-radius:4px;"></div>
                                <div class="vaga-label flex-grow-1">Idoso</div>
                                <i class="fas fa-user-friends text-warning"></i>
                            </div>
                            <div class="d-flex align-items-center p-2 border rounded bg-light">
                                <div class="vaga-indicador me-3" style="width:20px; height:20px; background-color:rgba(108,117,125,0.6); border-radius:4px;"></div>
                                <div class="vaga-label flex-grow-1">Deficiente</div>
                                <i class="fas fa-wheelchair text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- === TELA CHEIA === -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <button id="btnFullscreen" class="btn btn-outline-success w-100" title="Tela Cheia">
                            <i class="fas fa-expand me-2"></i>Visualização em Tela Cheia
                        </button>
                    </div>
                </div>

                <!-- === VOLTAR === -->
                <div class="mt-auto pt-4">
                    <div class="text-center">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100 py-3">
                            <i class="fas fa-arrow-left me-2"></i>Voltar para Dashboard
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal para Tela Cheia -->
<div id="fullscreenModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0">
            <div class="modal-header border-0 bg-dark">
                <h5 class="modal-title text-white">Visualização em Tela Cheia - Consulta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-dark position-relative">
                <div id="fullscreenViewer" 
                     style="width:100%; height:100%; background-repeat:no-repeat; background-position:center center; background-size:contain;"></div>
                <div id="fullscreenSetores" 
                     style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
                <div id="fullscreenVagas" 
                     style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Ícones SVG para as vagas -->
<svg style="display: none;">
    <symbol id="icon-car" viewBox="0 0 24 24">
        <path fill="currentColor" d="M5 11l1.5-4.5h11L19 11m-1.5 5a1.5 1.5 0 0 1-1.5-1.5a1.5 1.5 0 0 1 1.5-1.5a1.5 1.5 0 0 1 1.5 1.5a1.5 1.5 0 0 1-1.5 1.5m-11 0A1.5 1.5 0 0 1 5 14.5A1.5 1.5 0 0 1 6.5 13A1.5 1.5 0 0 1 8 14.5A1.5 1.5 0 0 1 6.5 16M18.92 6A1.5 1.5 0 0 0 17.5 5h-11A1.5 1.5 0 0 0 5.08 6L3 12v6a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1h12v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-6l-2.08-6z"/>
    </symbol>
    <symbol id="icon-motorcycle" viewBox="0 0 24 24">
        <path fill="currentColor" d="M17.42 10l-4.09-4.09L14.5 4.5L20 10m-2.5 4.5c-1.38 0-2.5 1.12-2.5 2.5s1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5s-1.12-2.5-2.5-2.5m-10 0c-1.38 0-2.5 1.12-2.5 2.5s1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5s-1.12-2.5-2.5-2.5M12 4c-3 0-5.68 1.38-7.44 3.54l2.09 2.09C7.61 8.5 9.72 7.5 12 7.5c2.28 0 4.39 1 5.85 2.63l2.09-2.09C17.68 5.38 15 4 12 4z"/>
    </symbol>
    <symbol id="icon-wheelchair" viewBox="0 0 24 24">
        <path fill="currentColor" d="M12 3a1.5 1.5 0 0 1 1.5 1.5A1.5 1.5 0 0 1 12 6a1.5 1.5 0 0 1-1.5-1.5A1.5 1.5 0 0 1 12 3m5 4a3 3 0 0 1 3 3a3 3 0 0 1-3 3c-.8 0-1.53-.33-2.06-.87L10.5 12.5l5 5L18 20h-6v-2l3.5-3.5l-4.15-4.15C10.93 10.35 10.5 11.14 10.5 12a3 3 0 0 1-3 3a3 3 0 0 1-3-3a3 3 0 0 1 3-3c.86 0 1.65.43 2.15 1.13l2.54-2.54l-1.44-1.44C9.47 7.33 8.7 7 7.83 7A3 3 0 0 0 4.83 10a3 3 0 0 0 3 3c.87 0 1.64-.33 2.24-.88l1.52 1.52l-3.76 3.76l-1.5-1.5l1.06-1.06l-1.06-1.06l-2.12 2.12l2.12 2.12l1.06-1.06l1.06 1.06l-1.06 1.06l1.5 1.5l4.76-4.76l2.54 2.54C16.53 17.67 17.3 18 18.17 18a3 3 0 0 0 3-3a3 3 0 0 0-3-3c-.86 0-1.65.43-2.15 1.13l-2.54-2.54l.44-.44C14.33 9.53 15 8.8 15 8a3 3 0 0 0-3-3a3 3 0 0 0-3 3h1.5a1.5 1.5 0 0 1 1.5-1.5a1.5 1.5 0 0 1 1.5 1.5a1.5 1.5 0 0 1-1.5 1.5c-.36 0-.7-.13-.96-.35l-1.44 1.44l2.54 2.54C12.35 13.53 13 14.2 13 15a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3c-.8 0-1.53.33-2.06.87l-2.54-2.54l1.44-1.44l4.12 4.12c.22-.26.35-.6.35-.96a1.5 1.5 0 0 1 1.5-1.5a1.5 1.5 0 0 1 1.5 1.5a1.5 1.5 0 0 1-1.5 1.5c-.36 0-.7-.13-.96-.35l-4.12-4.12C13.33 9.53 14 8.8 14 8a3 3 0 0 0-3-3a3 3 0 0 0-3 3h1.5A1.5 1.5 0 0 1 12 6.5a1.5 1.5 0 0 1 1.5 1.5a1.5 1.5 0 0 1-1.5 1.5c-.36 0-.7-.13-.96-.35L9.38 8.12C9.33 8.07 9 7.8 9 7.5A1.5 1.5 0 0 1 10.5 6a1.5 1.5 0 0 1 1.5 1.5c0 .3-.07.57-.12.62l1.44 1.44l2.54-2.54C15.47 7.33 16.2 7 17 7z"/>
    </symbol>
    <symbol id="icon-elderly" viewBox="0 0 24 24">
        <path fill="currentColor" d="M12.5 2a1.5 1.5 0 0 1 1.5 1.5a1.5 1.5 0 0 1-1.5 1.5a1.5 1.5 0 0 1-1.5-1.5A1.5 1.5 0 0 1 12.5 2M19 8c-1.1 0-2 .9-2 2v4.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5V10c0-.55.45-1 1-1s1 .45 1 1v4.5a3.5 3.5 0 0 1-3.5 3.5c-.71 0-1.39-.15-2-.42v2.67c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5v-2.67c-.61.27-1.29.42-2 .42A3.5 3.5 0 0 1 7 14.5V10c0-.55.45-1 1-1s1 .45 1 1v4.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5V10c0-1.1-.9-2-2-2s-2 .9-2 2v.5c0 .83-.67 1.5-1.5 1.5S5 11.33 5 10.5V10c0-2.21 1.79-4 4-4c1.1 0 2.09.45 2.81 1.17C12.91 6.45 13.9 6 15 6c2.21 0 4 1.79 4 4v4.5c0 .83-.67 1.5-1.5 1.5S16 11.33 16 10.5V10c0-.55-.45-1-1-1s-1 .45-1 1v4.5c0 1.65 1.35 3 3 3c1.08 0 2.09-.43 2.81-1.17C20.57 15.59 21 14.08 21 12.5V10c0-2.21-1.79-4-4-4z"/>
    </symbol>
</svg>

<!-- Adicionar Font Awesome para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Adicionar Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    .btn {
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .btn:active {
        transform: translateY(0);
    }
    
    .card {
        border-radius: 12px;
        transition: transform 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    
    .modal-fullscreen .modal-content {
        border-radius: 0;
    }
    
    #fullscreenViewer {
        background-color: #000;
    }
    
    /* Estilo para grid em tela cheia */
    .grid-cell-fullscreen {
        position: absolute;
        border: 1px solid rgba(0,132,255,0.2);
        background-color: transparent;
        pointer-events: none;
    }

    /* Indicador de zoom discreto */
    .zoom-indicator {
        transition: all 0.3s ease;
        opacity: 0.8;
    }
    
    .zoom-indicator:hover {
        opacity: 1;
    }

    /* Animação sutil para mudanças de zoom */
    @keyframes zoomPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .zoom-pulse {
        animation: zoomPulse 0.3s ease;
    }

    /* Ajustes para a estrutura Bootstrap */
    .container-fluid {
        height: 100vh;
    }
    
    .row.g-0 {
        margin-right: 0;
        margin-left: 0;
    }
    
    .row.g-0 > .col-9,
    .row.g-0 > .col-3 {
        padding-right: 0;
        padding-left: 0;
    }

    .grid-cell { 
        transition: background-color .06s linear; 
    }
    
    .vaga-icon { 
        transition: all 0.2s ease; 
    }
    
    .vaga-icon:hover { 
        transform: scale(1.1); 
    }
    
    .vaga-border { 
        transition: all 0.2s ease; 
    }
    
    .btn-group .btn { 
        border-radius: 0.375rem !important; 
        margin: 0 2px; 
    }

    /* Melhorias para legibilidade */
    .vaga-indicador {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .legenda-item {
        transition: all 0.3s ease;
    }
    
    .legenda-item:hover {
        transform: translateX(5px);
        background-color: rgba(0,123,255,0.05) !important;
    }
</style>

<script>
(async function(){
    // projetos vindo do controller
    @php
        $projetosJs = $projetos->map(function($p){
            return [
                'id' => $p->idProjeto,
                'nome' => $p->nomeProjeto,
                'caminhoUrl' => asset('storage/' . ltrim($p->caminhoPlantaEstacionamento ?? '', '/')),
                'caminhoRaw' => $p->caminhoPlantaEstacionamento,
            ];
        })->toArray();
    @endphp
    const projetos = @json($projetosJs);
    const viewer = document.getElementById('viewer');
    const setoresLayer = document.getElementById('setores-layer');
    const vagasLayer = document.getElementById('vagas-layer');
    const legendCard = document.getElementById('legendaSetoresCard');
    const legendContainer = document.getElementById('legendaSetores');
    const fullscreenViewer = document.getElementById('fullscreenViewer');
    const fullscreenSetores = document.getElementById('fullscreenSetores');
    const fullscreenVagas = document.getElementById('fullscreenVagas');
    const fullscreenModal = new bootstrap.Modal(document.getElementById('fullscreenModal'));
    const zoomLevelElement = document.getElementById('zoomLevel');

    let imgNaturalW = 1, imgNaturalH = 1;
    let baseBgW = 0, baseBgH = 0;
    let bgW = 0, bgH = 0, posX = 0, posY = 0, scaleFactor = 1;
    const MIN_SCALE = 0.5;
    const MAX_SCALE = 5;
    const rows = 80, cols = 80;
    const tipoColors = { 
        carro: 'rgba(0,123,255,0.6)', 
        moto: 'rgba(40,167,69,0.6)', 
        idoso: 'rgba(255,193,7,0.6)', 
        deficiente: 'rgba(108,117,125,0.6)' 
    };
    const tipoIcons = {
        carro: 'icon-car',
        moto: 'icon-motorcycle', 
        idoso: 'icon-elderly',
        deficiente: 'icon-wheelchair'
    };
    const sectorGrid = Array.from({ length: rows }, () => Array(cols).fill(null));
    let setorColors = {};
    let vagasData = [];
    
    // Estados dos filtros
    let showSetores = true;
    let showVagas = true;

    // === INICIALIZAÇÃO COM ZOOM PARA PREENCHER A VIEWPORT ===
    function initSizesAndPosition() {
        const containerW = viewer.clientWidth;
        const containerH = viewer.clientHeight;
        
        // Calcula escala para preencher a viewport (cover)
        const scaleX = containerW / imgNaturalW;
        const scaleY = containerH / imgNaturalH;
        
        // Usa a maior escala para preencher a viewport completamente
        const initialScale = Math.max(scaleX, scaleY) * 1.0;
        
        baseBgW = imgNaturalW;
        baseBgH = imgNaturalH;
        scaleFactor = initialScale;
        bgW = baseBgW * scaleFactor;
        bgH = baseBgH * scaleFactor;
        
        // Centraliza a imagem
        posX = (containerW - bgW) / 2;
        posY = (containerH - bgH) / 2;
        
        applyTransform();
        updateGrid();
        updateZoomIndicator();
        renderVagas();
    }

    function applyTransform() {
        viewer.style.backgroundSize = `${bgW}px ${bgH}px`;
        viewer.style.backgroundPosition = `${posX}px ${posY}px`;
        
        // Aplica transformação nas layers
        setoresLayer.style.transform = `translate(${posX}px, ${posY}px) scale(${scaleFactor})`;
        setoresLayer.style.transformOrigin = 'top left';
        vagasLayer.style.transform = `translate(${posX}px, ${posY}px) scale(${scaleFactor})`;
        vagasLayer.style.transformOrigin = 'top left';
    }

    function updateZoomIndicator() {
        const percentage = Math.round(scaleFactor * 100);
        zoomLevelElement.textContent = `${percentage}%`;
        
        // Adiciona animação sutil
        zoomLevelElement.classList.add('zoom-pulse');
        setTimeout(() => {
            zoomLevelElement.classList.remove('zoom-pulse');
        }, 300);
    }

    function clampPosition() {
        const cw = viewer.clientWidth;
        const ch = viewer.clientHeight;
        
        // Permite um pouco de overscroll para melhor UX
        const marginX = cw * 0.1;
        const marginY = ch * 0.1;
        
        posX = Math.min(marginX, Math.max(cw - bgW - marginX, posX));
        posY = Math.min(marginY, Math.max(ch - bgH - marginY, posY));
    }

    // === ZOOM ===
    viewer.addEventListener('wheel', e => {
        e.preventDefault();
        const zoomSpeed = 1.12;
        const delta = e.deltaY < 0 ? zoomSpeed : 1 / zoomSpeed;
        zoomToPoint(delta, e.clientX, e.clientY);
    });

    function zoomToPoint(delta, clientX, clientY) {
        const newScale = Math.min(MAX_SCALE, Math.max(MIN_SCALE, scaleFactor * delta));
        
        const rect = viewer.getBoundingClientRect();
        const mx = clientX - rect.left;
        const my = clientY - rect.top;
        
        // Calcula a posição relativa antes do zoom
        const relX = (mx - posX) / bgW;
        const relY = (my - posY) / bgH;

        // Aplica o zoom
        scaleFactor = newScale;
        bgW = baseBgW * scaleFactor;
        bgH = baseBgH * scaleFactor;

        // Ajusta a posição para manter o ponto sob o mouse
        posX = mx - relX * bgW;
        posY = my - relY * bgH;

        clampPosition();
        applyTransform();
        updateGrid();
        updateZoomIndicator();
        renderVagas();
    }

    // === CONTROLES DE ZOOM ===
    document.getElementById('btnZoomIn').addEventListener('click', () => {
        const rect = viewer.getBoundingClientRect();
        zoomToPoint(1.2, rect.left + rect.width / 2, rect.top + rect.height / 2);
    });

    document.getElementById('btnZoomOut').addEventListener('click', () => {
        const rect = viewer.getBoundingClientRect();
        zoomToPoint(1/1.2, rect.left + rect.width / 2, rect.top + rect.height / 2);
    });

    document.getElementById('btnZoomReset').addEventListener('click', () => {
        // Reset para 100% (escala natural)
        scaleFactor = 1;
        bgW = baseBgW * scaleFactor;
        bgH = baseBgH * scaleFactor;
        
        const cw = viewer.clientWidth;
        const ch = viewer.clientHeight;
        posX = (cw - bgW) / 2;
        posY = (ch - bgH) / 2;
        
        clampPosition();
        applyTransform();
        updateGrid();
        updateZoomIndicator();
        renderVagas();
    });

    // === TELA CHEIA ===
    document.getElementById('btnFullscreen').addEventListener('click', () => {
        fullscreenModal.show();
        // Wait for modal transition to complete
        setTimeout(() => {
            setupFullscreenView();
        }, 300);
    });

    function setupFullscreenView() {
        const containerW = fullscreenViewer.clientWidth;
        const containerH = fullscreenViewer.clientHeight;
        
        // Calculate scale to fit (contain)
        const scaleX = containerW / imgNaturalW;
        const scaleY = containerH / imgNaturalH;
        const scale = Math.min(scaleX, scaleY);
        
        // Calculate actual dimensions
        const actualWidth = imgNaturalW * scale;
        const actualHeight = imgNaturalH * scale;
        
        // Calculate centering offsets
        const offsetX = (containerW - actualWidth) / 2;
        const offsetY = (containerH - actualHeight) / 2;
        
        // Update fullscreen viewer
        fullscreenViewer.style.backgroundSize = 'contain';
        fullscreenViewer.style.backgroundPosition = 'center center';
        
        // Setup fullscreen layers
        setupFullscreenLayers(scale, actualWidth, actualHeight, offsetX, offsetY);
    }

    function setupFullscreenLayers(scale, actualWidth, actualHeight, offsetX, offsetY) {
        // Clear existing content
        fullscreenSetores.innerHTML = '';
        fullscreenVagas.innerHTML = '';
        
        const cellW = actualWidth / cols;
        const cellH = actualHeight / rows;
        
        // Create setores grid
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cell = document.createElement('div');
                cell.className = 'grid-cell-fullscreen';
                
                const left = offsetX + (c * cellW);
                const top = offsetY + (r * cellH);
                
                Object.assign(cell.style, {
                    width: `${cellW}px`,
                    height: `${cellH}px`,
                    left: `${left}px`,
                    top: `${top}px`,
                    border: '1px solid rgba(0,132,255,0.1)',
                    position: 'absolute'
                });
                
                // Apply sector color
                const setorNome = sectorGrid[r][c];
                if (setorNome && showSetores) {
                    cell.style.backgroundColor = setorColorWithAlpha(setorColors[setorNome]);
                }
                
                fullscreenSetores.appendChild(cell);
            }
        }
        
        // Create vagas
        if (showVagas) {
            renderFullscreenVagas(scale, actualWidth, actualHeight, offsetX, offsetY);
        }
    }

    function renderFullscreenVagas(scale, actualWidth, actualHeight, offsetX, offsetY) {
        const cellW = actualWidth / cols;
        const cellH = actualHeight / rows;

        vagasData.forEach(vaga => {
            const grids = vaga.grids || [];
            if (grids.length === 0) return;

            const minX = Math.min(...grids.map(g => Number(g.posicaoVagaX)));
            const maxX = Math.max(...grids.map(g => Number(g.posicaoVagaX)));
            const minY = Math.min(...grids.map(g => Number(g.posicaoVagaY)));
            const maxY = Math.max(...grids.map(g => Number(g.posicaoVagaY)));

            const width = (maxX - minX + 1) * cellW;
            const height = (maxY - minY + 1) * cellH;
            const left = offsetX + (minX * cellW);
            const top = offsetY + (minY * cellH);

            const centerX = (minX + maxX) / 2;
            const centerY = (minY + maxY) / 2;

            const vagaColor = tipoColors[vaga.tipoVaga] || tipoColors.carro;

            // Create border
            const border = document.createElement('div');
            Object.assign(border.style, {
                position: 'absolute',
                left: `${left}px`,
                top: `${top}px`,
                width: `${width}px`,
                height: `${height}px`,
                border: `2px solid ${vagaColor}`,
                backgroundColor: `${vagaColor}20`,
                borderRadius: '4px',
                pointerEvents: 'none',
                zIndex: '5'
            });
            fullscreenVagas.appendChild(border);

            // Create icon
            const icon = document.createElement('div');
            const baseIconSize = Math.min(cellW, cellH);
            const iconSize = baseIconSize * 2.0;
            const iconType = tipoIcons[vaga.tipoVaga] || 'icon-car';
            const rgbColor = vagaColor.replace('rgba(', '').replace(')', '').split(',');
            const iconColor = `rgb(${rgbColor[0]}, ${rgbColor[1]}, ${rgbColor[2]})`;

            Object.assign(icon.style, {
                position: 'absolute',
                left: `${offsetX + centerX * cellW + cellW/2 - iconSize/2}px`,
                top: `${offsetY + centerY * cellH + cellH/2 - iconSize/2}px`,
                width: `${iconSize}px`,
                height: `${iconSize}px`,
                color: iconColor,
                pointerEvents: 'none',
                zIndex: '10',
                filter: 'drop-shadow(1px 1px 2px rgba(0,0,0,0.3))'
            });

            icon.innerHTML = `
                <svg width="100%" height="100%" viewBox="0 0 24 24">
                    <use xlink:href="#${iconType}"></use>
                </svg>
            `;

            fullscreenVagas.appendChild(icon);
        });
    }

    // Add resize handler for fullscreen mode
    window.addEventListener('resize', () => {
        if (fullscreenModal._element.classList.contains('show')) {
            setupFullscreenView();
        }
    });

    // Inicializar controles de visualização
    function initControls() {
        const toggleSetores = document.getElementById('toggleSetores');
        const toggleVagas = document.getElementById('toggleVagas');
        
        toggleSetores.addEventListener('click', () => {
            showSetores = !showSetores;
            toggleSetores.classList.toggle('active', showSetores);
            toggleSetores.classList.toggle('btn-outline-primary', !showSetores);
            toggleSetores.classList.toggle('btn-primary', showSetores);
            updateLayersVisibility();
        });
        
        toggleVagas.addEventListener('click', () => {
            showVagas = !showVagas;
            toggleVagas.classList.toggle('active', showVagas);
            toggleVagas.classList.toggle('btn-outline-success', !showVagas);
            toggleVagas.classList.toggle('btn-success', showVagas);
            updateLayersVisibility();
        });
    }

    function updateLayersVisibility() {
        setoresLayer.style.display = showSetores ? 'block' : 'none';
        vagasLayer.style.display = showVagas ? 'block' : 'none';
        
        // Aplica transparência quando ambos estão visíveis
        if (showSetores && showVagas) {
            setoresLayer.style.opacity = '0.7';
            vagasLayer.style.opacity = '1';
        } else {
            setoresLayer.style.opacity = '1';
            vagasLayer.style.opacity = '1';
        }
        
        renderSetores();
        renderVagas();
    }

    // init grid
    function createGrid(){
        setoresLayer.innerHTML = '';
        vagasLayer.innerHTML = '';
        
        for(let r=0;r<rows;r++){
            for(let c=0;c<cols;c++){
                const cell = document.createElement('div');
                cell.className = 'grid-cell';
                cell.dataset.row = r; cell.dataset.col = c;
                Object.assign(cell.style, { 
                    position:'absolute', 
                    border:'1px solid rgba(0,0,0,0.03)', 
                    boxSizing:'border-box', 
                    pointerEvents:'none' 
                });
                setoresLayer.appendChild(cell);
            }
        }
        updateGrid();
    }

    function updateGrid(){
        const cellW = baseBgW / cols, cellH = baseBgH / rows;
        for(const cell of setoresLayer.children){
            const r = +cell.dataset.row, c = +cell.dataset.col;
            cell.style.width = `${cellW}px`; cell.style.height = `${cellH}px`;
            cell.style.left = `${c * cellW}px`; cell.style.top = `${r * cellH}px`;
        }
    }

    function renderSetores(){
        if (!showSetores) return;
        
        for(let r=0;r<rows;r++){
            for(let c=0;c<cols;c++){
                const cell = setoresLayer.querySelector(`.grid-cell[data-row='${r}'][data-col='${c}']`);
                if(!cell) continue;
                const setorNome = sectorGrid[r][c];
                cell.style.backgroundColor = setorNome ? setorColorWithAlpha(setorColors[setorNome]) : 'rgba(0,0,0,0.04)';
            }
        }
    }

    function setorColorWithAlpha(hexOrRgb){
        if(!hexOrRgb) return 'transparent';
        if(hexOrRgb.startsWith('rgba')) return hexOrRgb;
        try {
            const hex = hexOrRgb.replace('#','');
            const bigint = parseInt(hex,16);
            const r=(bigint>>16)&255, g=(bigint>>8)&255, b=bigint&255;
            return `rgba(${r},${g},${b},0.15)`;
        } catch { return hexOrRgb; }
    }

    // load setores from API and build legend
    async function loadSetores(idProjeto){
        try {
            const res = await fetch(`/api/setores/${idProjeto}`);
            const setores = await res.json();
            // reset
            for(let r=0;r<rows;r++) for(let c=0;c<cols;c++) sectorGrid[r][c]=null;
            setorColors = {};
            setores.forEach(s=>{
                if(s.nomeSetor) setorColors[s.nomeSetor] = s.corSetor || '#6c757d';
                if(s.setorCoordenadaY!=null && s.setorCoordenadaX!=null){
                    const ry = Number(s.setorCoordenadaY), cx = Number(s.setorCoordenadaX);
                    if(ry>=0 && ry<rows && cx>=0 && cx<cols) sectorGrid[ry][cx] = s.nomeSetor;
                }
            });
            buildLegend(Object.values(setorColors));
            renderSetores();
        } catch(e){
            console.error('Erro ao carregar setores:', e);
        }
    }

    function buildLegend(){
        legendContainer.innerHTML = '';
        const unique = Object.keys(setorColors);
        unique.forEach(nome=>{
            const el = document.createElement('div');
            el.className = 'd-flex align-items-center gap-2 p-2 border rounded legenda-item';
            el.style.minWidth = '120px';
            el.style.justifyContent = 'center';
            el.style.backgroundColor = 'rgba(255,255,255,0.8)';
            el.innerHTML = `<div style="width:20px;height:20px;border-radius:4px;background:${setorColors[nome]}; border:1px solid rgba(0,0,0,0.1);"></div>
                            <small style="font-weight:600">${nome}</small>`;
            legendContainer.appendChild(el);
        });
        legendCard.style.display = unique.length ? 'block' : 'none';
    }

    // load vagas and render borders + icons
    async function loadVagas(idProjeto){
        try {
            const res = await fetch(`/vagas/listar/${idProjeto}`);
            vagasData = await res.json();
            renderVagas();
        } catch(e){
            console.error('Erro ao carregar vagas:', e);
        }
    }

    function renderVagas() {
        if (!showVagas) return;
        
        vagasLayer.innerHTML = '';
        const cellW = baseBgW / cols;
        const cellH = baseBgH / rows;

        vagasData.forEach(vaga => {
            const grids = vaga.grids || [];
            if (grids.length === 0) return;

            // Calcula os limites da vaga
            const minX = Math.min(...grids.map(g => Number(g.posicaoVagaX)));
            const maxX = Math.max(...grids.map(g => Number(g.posicaoVagaX)));
            const minY = Math.min(...grids.map(g => Number(g.posicaoVagaY)));
            const maxY = Math.max(...grids.map(g => Number(g.posicaoVagaY)));

            const width = (maxX - minX + 1) * cellW;
            const height = (maxY - minY + 1) * cellH;
            const left = minX * cellW;
            const top = minY * cellH;

            const centerX = (minX + maxX) / 2;
            const centerY = (minY + maxY) / 2;

            const vagaColor = tipoColors[vaga.tipoVaga] || tipoColors.carro;

            // Cria a borda externa da vaga
            const border = document.createElement('div');
            border.className = 'vaga-border';
            Object.assign(border.style, {
                position: 'absolute',
                left: `${left}px`,
                top: `${top}px`,
                width: `${width}px`,
                height: `${height}px`,
                border: `2px solid ${vagaColor}`,
                backgroundColor: `${vagaColor}20`, // 20 = 12% de opacidade
                borderRadius: '4px',
                pointerEvents: 'none',
                zIndex: '5'
            });
            vagasLayer.appendChild(border);

            // Cria o ícone centralizado (200% maior)
            const icon = document.createElement('div');
            icon.className = 'vaga-icon';
            const baseIconSize = Math.min(cellW, cellH);
            const iconSize = baseIconSize * 2.0; // 200% maior
            const iconType = tipoIcons[vaga.tipoVaga] || 'icon-car';

            // Extrai a cor RGB para o ícone
            const rgbColor = vagaColor.replace('rgba(', '').replace(')', '').split(',');
            const iconColor = `rgb(${rgbColor[0]}, ${rgbColor[1]}, ${rgbColor[2]})`;

            Object.assign(icon.style, {
                position: 'absolute',
                left: `${centerX * cellW + cellW/2 - iconSize/2}px`,
                top: `${centerY * cellH + cellH/2 - iconSize/2}px`,
                width: `${iconSize}px`,
                height: `${iconSize}px`,
                color: iconColor,
                pointerEvents: 'none',
                zIndex: '10',
                filter: 'drop-shadow(1px 1px 2px rgba(0,0,0,0.3))'
            });

            icon.innerHTML = `
                <svg width="100%" height="100%" viewBox="0 0 24 24">
                    <use xlink:href="#${iconType}"></use>
                </svg>
            `;

            vagasLayer.appendChild(icon);
        });
    }

    // set projeto (load image, setores, vagas)
    function setProjeto(idProjeto){
        const proj = projectById(idProjeto);
        if(!proj) return;
        const src = proj.caminhoUrl || '';
        const img = new Image();
        img.src = src + '?v=' + Date.now();
        img.onload = () => {
            imgNaturalW = img.naturalWidth; imgNaturalH = img.naturalHeight;
            viewer.style.backgroundImage = `url(${img.src})`;
            fullscreenViewer.style.backgroundImage = `url(${img.src})`;
            initSizesAndPosition();
            createGrid();
            loadSetores(idProjeto);
            loadVagas(idProjeto);
        };
        img.onerror = () => {
            viewer.style.backgroundImage = '';
            fullscreenViewer.style.backgroundImage = '';
            setoresLayer.innerHTML = '';
            vagasLayer.innerHTML = '';
            console.error('Não foi possível carregar a planta:', src);
        };
    }

    function projectById(id){ return projetos.find(p=>p.id==id) ?? null; }

    // select handler
    const selectEl = document.getElementById('selectProjeto');
    if(selectEl){
        const initial = Number(selectEl.value) || (projetos[0] ? projetos[0].id : null);
        if(initial) setProjeto(initial);
        selectEl.addEventListener('change', ()=> {
            const id = Number(selectEl.value);
            setProjeto(id);
        });
    } else if(projetos.length){
        setProjeto(projetos[0].id);
    }

    // Inicializar controles
    initControls();
    updateLayersVisibility();

    window.addEventListener('resize', ()=> { 
        initSizesAndPosition(); 
    });

})();
</script>
@endsection