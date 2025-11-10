@extends('layouts.app')

@section('title', 'Cadastro de Vagas')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0">

        <!-- === VIEWPORT PRINCIPAL - COLUNA 8 === -->
        <div class="col-lg-8 col-md-7 position-relative" style="background-color:#f0f0f0; overflow:hidden; min-height: 70vh;">
            
            <!-- Indicador de Zoom -->
            <div class="zoom-indicator" style="position:absolute; top:10px; right:10px; z-index:1001; background:rgba(0,0,0,0.7); color:white; padding:4px 8px; border-radius:15px; font-size:11px; font-weight:500; backdrop-filter:blur(10px);">
                <span id="zoomLevel">100%</span>
            </div>
            
            <div id="viewer"
                 style="width:100%; height:100%; cursor:crosshair; user-select:none; background-repeat:no-repeat; background-position:center center; background-size:contain; position:relative;">
                <div id="grid-overlay"
                     style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;">
                </div>
                <!-- Elemento para seleção estilo Excel -->
                <div id="selection-rect" style="position:absolute; border:2px dashed #007bff; background-color:rgba(0,123,255,0.1); pointer-events:none; display:none; z-index:1000;"></div>
            </div>
        </div>

        <!-- === PAINEL DE CONTROLE - COLUNA 4 === -->
        <div class="col-lg-4 col-md-5 d-flex flex-column" style="background-color: #f8f9fa; border-left: 1px solid #dee2e6; min-height: 70vh;">
            <div class="control-panel h-100 d-flex flex-column p-3">
                
                <!-- Cabeçalho -->
                <div class="text-center mb-3">
                    <h5 class="text-primary mb-1">Painel de Controle</h5>
                    <p class="text-muted small mb-2">Gerencie as vagas do estacionamento</p>
                </div>

                <!-- === SISTEMA DE ABAS === -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <!-- Navegação por Abas -->
                        <ul class="nav nav-pills nav-justified mb-3" id="mainTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-2" style="font-size:0.8rem;" id="visualizacao-tab" data-bs-toggle="pill" data-bs-target="#visualizacao" type="button" role="tab">
                                    <i class="fas fa-expand-alt me-1"></i>Visualização
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-2" style="font-size:0.8rem;" id="edicao-tab" data-bs-toggle="pill" data-bs-target="#edicao" type="button" role="tab">
                                    <i class="fas fa-edit me-1"></i>Edição
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-2" style="font-size:0.8rem;" id="informacao-tab" data-bs-toggle="pill" data-bs-target="#informacao" type="button" role="tab">
                                    <i class="fas fa-info-circle me-1"></i>Informações
                                </button>
                            </li>
                        </ul>

                        <!-- Conteúdo das Abas -->
                        <div class="tab-content">
                            
                            <!-- === ABA VISUALIZAÇÃO === -->
                            <div class="tab-pane fade show active" id="visualizacao" role="tabpanel">
                                
                                <!-- Controles de Zoom -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-2">Zoom</label>
                                    <div class="d-flex gap-1 align-items-center">
                                        <button id="btnZoomOut" class="btn btn-outline-secondary flex-grow-1 py-1" title="Zoom Out">
                                            <i class="fas fa-search-minus"></i>
                                        </button>
                                        <button id="btnZoomReset" class="btn btn-outline-primary py-1" title="Reset Zoom" style="min-width: 50px;">
                                            100%
                                        </button>
                                        <button id="btnZoomIn" class="btn btn-outline-secondary flex-grow-1 py-1" title="Zoom In">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Controles de Navegação -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-2">Navegação</label>
                                    <div class="d-flex flex-column align-items-center">
                                        <button id="btnUp" class="btn btn-outline-secondary mb-1 py-1" title="Mover para Cima">
                                            <i class="fas fa-arrow-up"></i>
                                        </button>
                                        <div class="d-flex gap-1 w-100 justify-content-center">
                                            <button id="btnLeft" class="btn btn-outline-secondary flex-grow-1 py-1" title="Mover para Esquerda">
                                                <i class="fas fa-arrow-left"></i>
                                            </button>
                                            <button id="btnCenter" class="btn btn-outline-primary py-1" title="Centralizar">
                                                <i class="fas fa-bullseye"></i>
                                            </button>
                                            <button id="btnRight" class="btn btn-outline-secondary flex-grow-1 py-1" title="Mover para Direita">
                                                <i class="fas fa-arrow-right"></i>
                                            </button>
                                        </div>
                                        <button id="btnDown" class="btn btn-outline-secondary mt-1 py-1" title="Mover para Baixo">
                                            <i class="fas fa-arrow-down"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Tela Cheia -->
                                <div class="text-center mt-2">
                                    <button id="btnFullscreen" class="btn btn-outline-success w-100 py-1" title="Tela Cheia">
                                        <i class="fas fa-expand me-1"></i>Tela Cheia
                                    </button>
                                </div>
                            </div>

                            <!-- === ABA EDIÇÃO === -->
                            <div class="tab-pane fade" id="edicao" role="tabpanel">
                                
                                <!-- Tipos de Vaga -->
                                <div class="mb-3">
                                    <h6 class="text-center mb-2 text-secondary small">
                                        <i class="fas fa-tags me-1"></i>Tipos de Vaga
                                    </h6>
                                    <div id="tiposToolbar" class="d-flex flex-wrap justify-content-center gap-2"></div>
                                </div>

                                <!-- Ferramentas de Edição -->
                                <div class="mb-3">
                                    <h6 class="text-center mb-2 text-secondary small">Ferramentas de Edição</h6>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button id="btnLimpar" class="btn btn-outline-danger btn-sm px-3 py-1">
                                            <i class="fas fa-broom me-1"></i>Limpar
                                        </button>
                                        <button id="btnBorracha" class="btn btn-outline-warning btn-sm px-3 py-1">
                                            <i class="fas fa-eraser me-1"></i>Borracha
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- === ABA INFORMAÇÕES === -->
                            <div class="tab-pane fade" id="informacao" role="tabpanel">
                                
                                <!-- Info Setor Atual -->
                                <div class="mb-3">
                                    <h6 class="text-center mb-2 text-secondary small">
                                        <i class="fas fa-info-circle me-1"></i>Setor Atual
                                    </h6>
                                    <div id="setorInfo" class="text-center p-2 border rounded bg-light">
                                        <small class="text-muted">Selecione uma área no mapa</small>
                                    </div>
                                </div>

                                <!-- Legenda dos Setores -->
                                <div class="mb-3" id="legendaSetoresCard" style="display:none;">
                                    <h6 class="text-center mb-2 text-secondary small">
                                        <i class="fas fa-layer-group me-1"></i>Legenda dos Setores
                                    </h6>
                                    <div id="legendaSetores" class="d-flex flex-wrap justify-content-center gap-2"></div>
                                </div>

                                <!-- Estatísticas -->
                                <div class="text-center p-2 border rounded bg-light">
                                    <small class="text-muted d-block">Total de Vagas</small>
                                    <h5 class="text-primary mb-0" id="totalVagas">0</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- === VAGAS TEMPORÁRIAS === -->
                <div class="card border-0 shadow-sm mb-3 flex-grow-1">
                    <div class="card-body p-3 d-flex flex-column h-100">
                        <h6 class="text-center mb-2 text-secondary small">
                            <i class="fas fa-list me-1"></i>Vagas Temporárias
                        </h6>
                        <div id="vagasList" class="flex-grow-1" style="min-height:60px; max-height:200px; overflow:auto;"></div>
                    </div>
                </div>

                <!-- === SALVAR === -->
                <div class="mt-auto pt-3">
                    <div class="text-center">
                        <button id="btnSalvar" class="btn btn-success w-100 py-2 shadow">
                            <i class="fas fa-save me-1"></i>Salvar Vagas
                        </button>
                    </div>
                    <div class="text-center mt-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100 py-2">
                            <i class="fas fa-arrow-left me-1"></i>Voltar para Dashboard
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
            <div class="modal-header border-0 bg-dark py-2">
                <h6 class="modal-title text-white">Visualização em Tela Cheia</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-dark position-relative">
                <div id="fullscreenViewer" 
                     style="width:100%; height:100%; background-repeat:no-repeat; background-position:center center; background-size:contain;"></div>
                <div id="fullscreenGrid" 
                     style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Adicionar Font Awesome para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Adicionar Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    .btn {
        transition: all 0.3s ease;
        border-radius: 6px;
        font-size: 0.875rem;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .btn:active {
        transform: translateY(0);
    }
    
    .card {
        border-radius: 8px;
        transition: transform 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-1px);
    }
    
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    
    #setoresContainer div {
        transition: all 0.3s ease;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8rem;
        width: 60px !important;
        height: 60px !important;
    }
    
    #setoresContainer div:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
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

    /* Garantir que a imagem seja visível */
    #viewer {
        background-color: #f0f0f0;
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
        max-height: 100vh;
        overflow: hidden;
    }
    
    .row.g-0 {
        margin-right: 0;
        margin-left: 0;
    }
    
    .row.g-0 > .col-lg-8,
    .row.g-0 > .col-lg-4,
    .row.g-0 > .col-md-7,
    .row.g-0 > .col-md-5 {
        padding-right: 0;
        padding-left: 0;
    }

    /* Ajustes responsivos */
    @media (max-width: 768px) {
        .col-md-7, .col-md-5 {
            min-height: 50vh !important;
        }
        
        .control-panel {
            padding: 1rem !important;
        }
        
        .card-body {
            padding: 0.75rem !important;
        }
    }

    /* Melhorar scroll quando necessário */
    .control-panel {
        scrollbar-width: thin;
        scrollbar-color: #007bff #f1f1f1;
    }

    .control-panel::-webkit-scrollbar {
        width: 6px;
    }

    .control-panel::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .control-panel::-webkit-scrollbar-thumb {
        background: #007bff;
        border-radius: 3px;
    }

    .grid-cell.selecting {
        mix-blend-mode: normal;
    }
    
    .grid-cell {
        transition: background-color 0.06s linear;
    }

    /* Estilo para badges de tipos de vaga */
    .badge {
        font-size: 0.7em;
    }

    /* Estilo para as abas */
    .nav-pills .nav-link {
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link.active {
        background-color: #007bff;
        font-weight: 600;
    }

    .nav-pills .nav-link:not(.active) {
        color: #6c757d;
        background-color: rgba(0,123,255,0.1);
    }

    .nav-pills .nav-link:not(.active):hover {
        background-color: rgba(0,123,255,0.2);
        color: #007bff;
    }

    /* Conteúdo das abas */
    .tab-content {
        min-height: 200px;
    }

    .tab-pane {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Ajustes para elementos compactos */
    #legendaSetores .d-flex {
        min-width: 100px !important;
        padding: 6px 8px !important;
    }

    #tiposToolbar .btn {
        min-width: 80px !important;
        font-size: 0.8rem;
    }

    #vagasList .d-flex {
        padding: 6px 8px !important;
        margin-bottom: 4px !important;
    }

    /* Estatísticas */
    #totalVagas {
        font-weight: bold;
    }
</style>

<script>
/*
  VERSÃO 1 COM SISTEMA DE ABAS
  - Mantém o layout limpo e organizado da primeira versão
  - Adiciona sistema de abas para melhor organização
  - Preserva toda a funcionalidade original
*/

(async function () {
    // === VARIÁVEIS GERAIS ===
    const imageUrl = @json(asset('storage/' . $projeto->caminhoPlantaEstacionamento));
    const viewer = document.getElementById('viewer');
    const gridOverlay = document.getElementById('grid-overlay');
    const selectionRect = document.getElementById('selection-rect');
    const fullscreenViewer = document.getElementById('fullscreenViewer');
    const fullscreenGrid = document.getElementById('fullscreenGrid');
    const fullscreenModal = new bootstrap.Modal(document.getElementById('fullscreenModal'));
    const zoomLevelElement = document.getElementById('zoomLevel');

    // imagem natural e dimensões base
    let imgNaturalW = 1, imgNaturalH = 1;
    let baseBgW = 0, baseBgH = 0;
    let scaleFactor = 1;
    const MIN_SCALE = 1;
    const MAX_SCALE = 6;

    let bgW = 0, bgH = 0;
    let posX = 0, posY = 0;
    let dragging = false;
    let startClientX = 0, startClientY = 0;
    let startPosX = 0, startPosY = 0;

    // Grid: tamanho e dados
    const rows = 80;
    const cols = 80;

    // Mapa de setores por coordenada
    const sectorMap = new Map(); // key: "x,y" -> setor data
    let setorColors = {};
    let sectoresList = [];

    // Vagas temporárias
    let vagas = [];
    let vagaCounter = 0;

    // Tipos de vaga e cores
    const tipoColors = {
        carro: 'rgba(0,123,255,0.6)',
        moto: 'rgba(40,167,69,0.6)',
        idoso: 'rgba(255,193,7,0.6)',
        deficiente: 'rgba(108,117,125,0.6)'
    };
    let currentTipo = 'carro';

    // Setor atual (detectado automaticamente)
    let currentSetor = null;

    // Estado de seleção
    let selecting = false;
    let selStart = null;
    let selEnd = null;
    let eraserMode = false;

    // === CARREGAR IMAGEM ===
    const img = new Image();
    img.src = imageUrl + '?v=' + Date.now();
    img.onload = () => {
        imgNaturalW = img.naturalWidth;
        imgNaturalH = img.naturalHeight;
        viewer.style.backgroundImage = `url(${imageUrl})`;
        fullscreenViewer.style.backgroundImage = `url(${imageUrl})`;
        initSizesAndPosition();
        createGrid();
        setupFullscreenGrid();
        loadSetores();
    };

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
    }

    function applyTransform() {
        viewer.style.backgroundSize = `${bgW}px ${bgH}px`;
        viewer.style.backgroundPosition = `${posX}px ${posY}px`;
        
        // Aplica transformação no grid overlay
        gridOverlay.style.transform = `translate(${posX}px, ${posY}px) scale(${scaleFactor})`;
        gridOverlay.style.transformOrigin = 'top left';
        
        // NÃO aplicar transform no selectionRect (vamos posicioná-lo em coordenadas de tela)
        selectionRect.style.transform = 'none';
        selectionRect.style.transformOrigin = 'top left';
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
        renderAll();
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
        renderAll();
    });

    // === CONTROLES DE NAVEGAÇÃO ===
    document.getElementById('btnUp').addEventListener('click', () => {
        posY += 50;
        clampPosition();
        applyTransform();
    });

    document.getElementById('btnDown').addEventListener('click', () => {
        posY -= 50;
        clampPosition();
        applyTransform();
    });

    document.getElementById('btnLeft').addEventListener('click', () => {
        posX += 50;
        clampPosition();
        applyTransform();
    });

    document.getElementById('btnRight').addEventListener('click', () => {
        posX -= 50;
        clampPosition();
        applyTransform();
    });

    document.getElementById('btnCenter').addEventListener('click', () => {
        const cw = viewer.clientWidth;
        const ch = viewer.clientHeight;
        posX = (cw - bgW) / 2;
        posY = (ch - bgH) / 2;
        clampPosition();
        applyTransform();
    });

    // === TELA CHEIA ===
    document.getElementById('btnFullscreen').addEventListener('click', () => {
        fullscreenModal.show();
        // Wait for modal transition to complete
        setTimeout(() => {
            setupFullscreenGrid();
        }, 300);
    });

    function calculateFullscreenDimensions() {
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
        
        return { scale, actualWidth, actualHeight, offsetX, offsetY };
    }

    function setupFullscreenGrid() {
        const { scale, actualWidth, actualHeight, offsetX, offsetY } = calculateFullscreenDimensions();
        
        // Clear existing grid
        fullscreenGrid.innerHTML = '';
        
        // Calculate cell dimensions based on actual image size
        const cellW = actualWidth / cols;
        const cellH = actualHeight / rows;
        
        // Create grid cells with correct positioning
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cell = document.createElement('div');
                cell.className = 'grid-cell-fullscreen';
                
                // Position relative to centered image
                const left = offsetX + (c * cellW);
                const top = offsetY + (r * cellH);
                
                Object.assign(cell.style, {
                    width: `${cellW}px`,
                    height: `${cellH}px`,
                    left: `${left}px`,
                    top: `${top}px`,
                    border: '1px solid rgba(0,132,255,0.2)',
                    position: 'absolute'
                });
                
                fullscreenGrid.appendChild(cell);
            }
        }
        
        // Update fullscreen viewer background size
        fullscreenViewer.style.backgroundSize = 'contain';
        fullscreenViewer.style.backgroundPosition = 'center center';
    }

    // Add resize handler for fullscreen mode
    window.addEventListener('resize', () => {
        if (fullscreenModal._element.classList.contains('show')) {
            setupFullscreenGrid();
        }
    });

    // === ARRASTAR (MIDDLE MOUSE) ===
    viewer.addEventListener('mousedown', e => {
        // Middle mouse button para arrastar
        if (e.button === 1) {
            e.preventDefault();
            dragging = true;
            startClientX = e.clientX;
            startClientY = e.clientY;
            startPosX = posX;
            startPosY = posY;
            viewer.style.cursor = 'grabbing';
        }
    });

    window.addEventListener('mousemove', e => {
        if (dragging) {
            posX = startPosX + (e.clientX - startClientX);
            posY = startPosY + (e.clientY - startClientY);
            clampPosition();
            applyTransform();
        } else if (selecting) {
            const cell = getCellFromClientPoint(e.clientX, e.clientY);
            if (cell) {
                selEnd = { r: +cell.dataset.row, c: +cell.dataset.col };
                highlightSelection();
                // Atualiza info do setor durante a seleção
                updateSetorInfo(selStart);
            }
        }
    });

    window.addEventListener('mouseup', e => {
        if (e.button === 1 && dragging) {
            dragging = false;
            viewer.style.cursor = 'crosshair';
        }

        if (selecting && e.button === 0) {
            finalizeSelection();
            selecting = false;
            selStart = selEnd = null;
            removeSelectionHighlight();
        }
    });

    // === GRID CREATION ===
    function createGrid() {
        gridOverlay.innerHTML = '';
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cell = document.createElement('div');
                cell.className = 'grid-cell';
                cell.dataset.row = r;
                cell.dataset.col = c;
                Object.assign(cell.style, {
                    position: 'absolute',
                    border: '1px solid rgba(0,0,0,0.04)',
                    boxSizing: 'border-box',
                    pointerEvents: 'auto'
                });

                cell.addEventListener('mousedown', e => {
                    if (e.button !== 0) return;
                    e.preventDefault();
                    
                    if (eraserMode) {
                        const vagaId = cell.dataset.vagaId;
                        if (vagaId) removeVagaById(Number(vagaId));
                        return;
                    }

                    selecting = true;
                    selStart = { r: +cell.dataset.row, c: +cell.dataset.col };
                    selEnd = { r: selStart.r, c: selStart.c };
                    highlightSelection();
                    updateSetorInfo(selStart);
                });

                gridOverlay.appendChild(cell);
            }
        }
        updateGrid();
    }

    function updateGrid() {
        const cellW = baseBgW / cols;
        const cellH = baseBgH / rows;
        for (const cell of gridOverlay.children) {
            const r = +cell.dataset.row;
            const c = +cell.dataset.col;
            cell.style.width = `${cellW}px`;
            cell.style.height = `${cellH}px`;
            cell.style.left = `${c * cellW}px`;
            cell.style.top = `${r * cellH}px`;
        }
        renderAll();
    }

    function getCellFromClientPoint(clientX, clientY) {
        const rect = viewer.getBoundingClientRect();
        const localX = (clientX - rect.left - posX) / (bgW / baseBgW);
        const localY = (clientY - rect.top - posY) / (bgH / baseBgH);
        if (localX < 0 || localY < 0) return null;
        const cellW = baseBgW / cols;
        const cellH = baseBgH / rows;
        const c = Math.floor(localX / cellW);
        const r = Math.floor(localY / cellH);
        if (r < 0 || c < 0 || r >= rows || c >= cols) return null;
        return gridOverlay.querySelector(`.grid-cell[data-row='${r}'][data-col='${c}']`);
    }

    // === IDENTIFICAÇÃO AUTOMÁTICA DO SETOR ===
    function findSetorByCoordinate(x, y) {
        const key = `${x},${y}`;
        return sectorMap.get(key) || null;
    }

    function updateSetorInfo(startCoord) {
        const setor = findSetorByCoordinate(startCoord.c, startCoord.r);
        const setorInfoEl = document.getElementById('setorInfo');
        
        if (setor) {
            currentSetor = setor;
            setorInfoEl.innerHTML = `
                <div style="background:${setor.corSetor}; width:16px; height:16px; border-radius:3px; display:inline-block; vertical-align:middle;"></div>
                <strong style="font-size:0.9rem">${setor.nomeSetor}</strong>
            `;
            setorInfoEl.className = 'text-center p-2 border rounded';
            setorInfoEl.style.backgroundColor = setorColorWithAlpha(setor.corSetor);
        } else {
            currentSetor = null;
            setorInfoEl.innerHTML = '<small class="text-danger" style="font-size:0.8rem">⚠️ Nenhum setor encontrado</small>';
            setorInfoEl.className = 'text-center p-2 border rounded bg-light';
        }
    }

    function highlightSelection() {
        removeSelectionHighlight();
        if (!selStart || !selEnd) return;
        const r1 = Math.min(selStart.r, selEnd.r);
        const r2 = Math.max(selStart.r, selEnd.r);
        const c1 = Math.min(selStart.c, selEnd.c);
        const c2 = Math.max(selStart.c, selEnd.c);
        
        for (let r = r1; r <= r2; r++) {
            for (let c = c1; c <= c2; c++) {
                const cell = gridOverlay.querySelector(`.grid-cell[data-row='${r}'][data-col='${c}']`);
                if (!cell) continue;
                cell.classList.add('selecting');
                cell.style.outline = '2px dashed rgba(0,0,0,0.15)';
            }
        }
    }

    function removeSelectionHighlight() {
        gridOverlay.querySelectorAll('.grid-cell.selecting').forEach(cell => {
            cell.classList.remove('selecting');
            cell.style.outline = '';
        });
    }

    function finalizeSelection() {
        if (!selStart || !selEnd) return;
        const r1 = Math.min(selStart.r, selEnd.r);
        const r2 = Math.max(selStart.r, selEnd.r);
        const c1 = Math.min(selStart.c, selEnd.c);
        const c2 = Math.max(selStart.c, selEnd.c);
        const height = r2 - r1 + 1;
        const width = c2 - c1 + 1;
        
        if (width * height <= 1) return;

        // Verifica se há setor válido
        if (!currentSetor) {
            alert('⚠️ Não é possível criar vaga nesta área. Nenhum setor encontrado.');
            return;
        }

        const posicoes = [];
        for (let r = r1; r <= r2; r++) {
            for (let c = c1; c <= c2; c++) {
                posicoes.push({ x: c, y: r });
            }
        }

        vagaCounter++;
        const vagaId = Date.now() + vagaCounter;
        const nomeVaga = `Vaga ${vagaCounter}`;
        const vaga = {
            id: vagaId,
            nomeVaga,
            tipoVaga: currentTipo,
            idSetor: currentSetor.idSetor,
            posicoes
        };
        vagas.push(vaga);

        markCellsForVaga(vaga);
        updateVagasList();
    }

    function markCellsForVaga(vaga) {
        const color = tipoColors[vaga.tipoVaga] || tipoColors.carro;
        vaga.posicoes.forEach(p => {
            const cell = gridOverlay.querySelector(`.grid-cell[data-row='${p.y}'][data-col='${p.x}']`);
            if (!cell) return;
            cell.dataset.vagaId = vaga.id;
            cell.style.backgroundColor = color;
        });
    }

    function removeVagaById(id) {
        const idx = vagas.findIndex(v => v.id === id);
        if (idx === -1) return;
        const vaga = vagas[idx];
        
        vaga.posicoes.forEach(p => {
            const cell = gridOverlay.querySelector(`.grid-cell[data-row='${p.y}'][data-col='${p.x}']`);
            if (!cell) return;
            delete cell.dataset.vagaId;
            const setor = findSetorByCoordinate(p.x, p.y);
            cell.style.backgroundColor = setor ? setorColorWithAlpha(setor.corSetor) : 'rgba(0,0,0,0.04)';
        });
        
        vagas.splice(idx, 1);
        updateVagasList();
    }

    function clearAllVagas() {
        vagas.forEach(vaga => {
            vaga.posicoes.forEach(p => {
                const cell = gridOverlay.querySelector(`.grid-cell[data-row='${p.y}'][data-col='${p.x}']`);
                if (!cell) return;
                delete cell.dataset.vagaId;
                const setor = findSetorByCoordinate(p.x, p.y);
                cell.style.backgroundColor = setor ? setorColorWithAlpha(setor.corSetor) : 'rgba(0,0,0,0.04)';
            });
        });
        vagas = [];
        vagaCounter = 0;
        updateVagasList();
    }

    function updateVagasList() {
        const container = document.getElementById('vagasList');
        const totalElement = document.getElementById('totalVagas');
        container.innerHTML = '';
        vagas.forEach(v => {
            const setor = sectoresList.find(s => s.idSetor === v.idSetor);
            const div = document.createElement('div');
            div.className = 'd-flex justify-content-between align-items-center p-2 mb-2 border rounded';
            div.style.backgroundColor = 'rgba(248,249,250,0.8)';
            div.innerHTML = `
                <div class="flex-grow-1">
                    <strong class="d-block" style="font-size:0.85rem">${v.nomeVaga}</strong>
                    <small class="text-muted d-block" style="font-size:0.75rem">Setor: ${setor ? setor.nomeSetor : 'N/A'}</small>
                    <span class="badge bg-primary" style="font-size:0.65rem">${v.tipoVaga}</span>
                </div>
            `;
            const btn = document.createElement('button');
            btn.className = 'btn btn-sm btn-outline-danger ms-2';
            btn.innerHTML = '<i class="fas fa-times"></i>';
            btn.title = 'Remover vaga';
            btn.addEventListener('click', () => removeVagaById(v.id));
            div.appendChild(btn);
            container.appendChild(div);
        });
        totalElement.textContent = vagas.length;
    }

    function setorColorWithAlpha(hexOrRgb) {
        if (!hexOrRgb) return 'transparent';
        if (hexOrRgb.startsWith('rgba')) return hexOrRgb;
        try {
            const hex = hexOrRgb.replace('#', '');
            const bigint = parseInt(hex, 16);
            const r = (bigint >> 16) & 255;
            const g = (bigint >> 8) & 255;
            const b = bigint & 255;
            return `rgba(${r},${g},${b},0.15)`;
        } catch (e) {
            return hexOrRgb;
        }
    }

    function renderAll() {
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cell = gridOverlay.querySelector(`.grid-cell[data-row='${r}'][data-col='${c}']`);
                if (!cell) continue;
                
                const vagaId = cell.dataset.vagaId;
                if (vagaId) {
                    const vagaObj = vagas.find(v => String(v.id) === String(vagaId));
                    cell.style.backgroundColor = vagaObj ? (tipoColors[vagaObj.tipoVaga] || tipoColors.carro) : 'rgba(0,0,0,0.06)';
                } else {
                    const setor = findSetorByCoordinate(c, r);
                    cell.style.backgroundColor = setor ? setorColorWithAlpha(setor.corSetor) : 'rgba(0,0,0,0.04)';
                }
            }
        }
    }

    // === LOAD SETORES - Agora mapeia por coordenadas ===
    async function loadSetores() {
        try {
            const response = await fetch(`/api/setores/{{ $projeto->idProjeto }}`);
            const setores = await response.json();
            sectoresList = setores;

            // Limpa o mapa anterior
            sectorMap.clear();
            setorColors = {};

            // Preenche o mapa de setores por coordenada
            setores.forEach(s => {
                if (s.setorCoordenadaY != null && s.setorCoordenadaX != null) {
                    const key = `${s.setorCoordenadaX},${s.setorCoordenadaY}`;
                    sectorMap.set(key, s);
                    setorColors[s.nomeSetor] = s.corSetor || '#6c757d';
                }
            });

            buildLegend(setores);
            renderAll();
        } catch (err) {
            console.error('Erro ao carregar setores:', err);
        }
    }

    function buildLegend(setores) {
        const legendaCard = document.getElementById('legendaSetoresCard');
        const container = document.getElementById('legendaSetores');
        container.innerHTML = '';
        const unique = [...new Map(setores.map(s => [s.nomeSetor, s])).values()];
        unique.forEach(s => {
            const el = document.createElement('div');
            el.className = 'd-flex align-items-center gap-2 p-2 border rounded';
            el.style.minWidth = '100px';
            el.style.justifyContent = 'center';
            el.style.backgroundColor = 'rgba(255,255,255,0.8)';
            el.innerHTML = `<div style="width:16px;height:16px;border-radius:4px;background:${s.corSetor}; border:1px solid rgba(0,0,0,0.1);"></div>
                            <small style="font-weight:600; font-size:0.8rem">${s.nomeSetor}</small>`;
            container.appendChild(el);
        });
        if (unique.length) legendaCard.style.display = 'block';
    }

    function buildTiposToolbar() {
        const toolbar = document.getElementById('tiposToolbar');
        toolbar.innerHTML = '';
        Object.keys(tipoColors).forEach((tipo) => {
            const btn = document.createElement('button');
            btn.className = 'btn ' + (tipo === currentTipo ? 'btn-primary' : 'btn-outline-primary');
            btn.style.minWidth = '80px';
            btn.innerHTML = `<i class="fas fa-${getTipoIcon(tipo)} me-1"></i><span style="font-size:0.8rem">${tipo.toUpperCase()}</span>`;
            btn.addEventListener('click', () => {
                currentTipo = tipo;
                Array.from(toolbar.children).forEach(b => b.classList.remove('btn-primary'));
                Array.from(toolbar.children).forEach(b => b.classList.add('btn-outline-primary'));
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
            });
            toolbar.appendChild(btn);
        });
    }

    function getTipoIcon(tipo) {
        const icons = {
            carro: 'car',
            moto: 'motorcycle',
            idoso: 'user-friends',
            deficiente: 'wheelchair'
        };
        return icons[tipo] || 'square';
    }

    buildTiposToolbar();

    // Controles
    document.getElementById('btnLimpar').addEventListener('click', () => {
        if (!confirm('Remover todas as vagas temporárias?')) return;
        clearAllVagas();
    });

    document.getElementById('btnBorracha').addEventListener('click', () => {
        eraserMode = !eraserMode;
        const btn = document.getElementById('btnBorracha');
        btn.classList.toggle('btn-warning', eraserMode);
        btn.classList.toggle('btn-outline-warning', !eraserMode);
    });

    document.getElementById('btnSalvar').addEventListener('click', async () => {
        if (vagas.length === 0) return alert('⚠️ Nenhuma vaga definida!');
        
        const payload = {
            idProjeto: @json($projeto->idProjeto),
            vagas: vagas.map((v) => ({
                idSetor: v.idSetor,
                nomeVaga: v.nomeVaga,
                tipoVaga: v.tipoVaga,
                coordenadas: v.posicoes
            }))
        };
        
        try {
            const response = await fetch("{{ route('vagas.store') }}", {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            if (result.success) {
                alert('✅ Vagas salvas com sucesso! Agora você será redirecionado para associar sensores.');
                clearAllVagas();
                
                // Redireciona para a view de associar sensores
                window.location.href = "{{ route('vagas.inteligentes.associar') }}";
            } else {
                alert('⚠️ Erro ao salvar: ' + (result.message || 'Verifique servidor.'));
            }
        } catch (err) {
            console.error(err);
            alert('❌ Falha ao comunicar com o servidor.');
        }
    });

    window.addEventListener('resize', () => {
        initSizesAndPosition();
    });

    function renderGridCells() {
        updateGrid();
        renderAll();
    }

    renderGridCells();

})();
</script>
@endsection