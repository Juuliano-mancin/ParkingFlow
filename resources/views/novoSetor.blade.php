@extends('layouts.app')

@section('title', 'Novo Setor')

@section('content')
<div class="container-fluid p-0 vh-100">
    <div class="row g-0 h-100">

        <!-- === VIEWPORT PRINCIPAL - COLUNA 8 === -->
        <div class="col-8 position-relative" style="background-color:#f0f0f0; overflow:hidden;">
            
            <!-- Indicador de Zoom -->
            <div class="zoom-indicator" style="position:absolute; top:15px; right:15px; z-index:1001; background:rgba(0,0,0,0.7); color:white; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:500; backdrop-filter:blur(10px);">
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
        <div class="col-4 d-flex flex-column" style="background-color: #f8f9fa; border-left: 1px solid #dee2e6;">
            <div class="control-panel h-100 d-flex flex-column p-4 overflow-auto">
                
                <!-- Cabeçalho -->
                <div class="text-center mb-4">
                    <h4 class="text-primary mb-2">Painel de Controle</h4>
                    <p class="text-muted small">Gerencie os setores do estacionamento</p>
                </div>

                <!-- === CONTROLES DE VIEWPORT === -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="text-center mb-3 text-secondary">
                            <i class="fas fa-expand-alt me-2"></i>Controles da Visualização
                        </h6>
                        
                        <!-- Controles de Zoom -->
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-2">Zoom</label>
                            <div class="d-flex gap-2 align-items-center">
                                <button id="btnZoomOut" class="btn btn-outline-secondary flex-grow-1" title="Zoom Out">
                                    <i class="fas fa-search-minus"></i>
                                </button>
                                <button id="btnZoomReset" class="btn btn-outline-primary" title="Reset Zoom" style="min-width: 60px;">
                                    100%
                                </button>
                                <button id="btnZoomIn" class="btn btn-outline-secondary flex-grow-1" title="Zoom In">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Controles de Navegação -->
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-2">Navegação</label>
                            <div class="d-flex flex-column align-items-center">
                                <button id="btnUp" class="btn btn-outline-secondary mb-1" title="Mover para Cima">
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <div class="d-flex gap-1 w-100 justify-content-center">
                                    <button id="btnLeft" class="btn btn-outline-secondary flex-grow-1" title="Mover para Esquerda">
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                    <button id="btnCenter" class="btn btn-outline-primary" title="Centralizar">
                                        <i class="fas fa-bullseye"></i>
                                    </button>
                                    <button id="btnRight" class="btn btn-outline-secondary flex-grow-1" title="Mover para Direita">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                                <button id="btnDown" class="btn btn-outline-secondary mt-1" title="Mover para Baixo">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tela Cheia -->
                        <div class="text-center mt-3">
                            <button id="btnFullscreen" class="btn btn-outline-success w-100" title="Tela Cheia">
                                <i class="fas fa-expand me-2"></i>Tela Cheia
                            </button>
                        </div>
                    </div>
                </div>

                <!-- === GERAR SETORES === -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-3">
                            <i class="fas fa-layer-group me-2"></i>Gerar Setores
                        </h5>
                        <div class="input-group mb-3">
                            <input type="number" id="numSetores" class="form-control border-primary" placeholder="Qtd. de setores" min="1" max="10">
                            <button id="btnGerar" class="btn btn-primary">
                                <i class="fas fa-magic me-1"></i>Gerar
                            </button>
                        </div>
                        <small class="text-muted d-block text-center">
                            <i class="fas fa-info-circle me-1"></i>Máximo: 10 setores
                        </small>
                    </div>
                </div>

                <!-- === LISTA DE SETORES === -->
                <div class="mb-4">
                    <h6 class="text-center mb-3 text-secondary">
                        <i class="fas fa-palette me-2"></i>Setores Criados
                    </h6>
                    <div id="setoresContainer" class="d-flex flex-wrap justify-content-center gap-3"></div>
                </div>

                <!-- === CONTROLES DE EDIÇÃO === -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="text-center mb-3 text-secondary">Ferramentas de Edição</h6>
                        <div class="d-flex justify-content-center gap-3">
                            <button id="btnLimpar" class="btn btn-outline-danger btn-lg px-4 py-2">
                                <i class="fas fa-broom me-2"></i>Limpar
                            </button>
                            <button id="btnBorracha" class="btn btn-outline-warning btn-lg px-4 py-2">
                                <i class="fas fa-eraser me-2"></i>Borracha
                            </button>
                        </div>
                    </div>
                </div>

                <!-- === SALVAR === -->
                <div class="mt-auto pt-4">
                    <div class="text-center">
                        <button id="btnSalvar" class="btn btn-success btn-lg w-100 py-3 shadow">
                            <i class="fas fa-save me-2"></i>Salvar Setores
                        </button>
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
                <h5 class="modal-title text-white">Visualização em Tela Cheia</h5>
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
    
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    
    #setoresContainer div {
        transition: all 0.3s ease;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    #setoresContainer div:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
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
        height: 100vh;
    }
    
    .row.g-0 {
        margin-right: 0;
        margin-left: 0;
    }
    
    .row.g-0 > .col-8,
    .row.g-0 > .col-4 {
        padding-right: 0;
        padding-left: 0;
    }
</style>

<script>
(function () {
    // === VARIÁVEIS GERAIS ===
    const imageUrl = @json(asset($caminhoPublico));
    const viewer = document.getElementById('viewer');
    const gridOverlay = document.getElementById('grid-overlay');
    const selectionRect = document.getElementById('selection-rect');
    const fullscreenViewer = document.getElementById('fullscreenViewer');
    const fullscreenGrid = document.getElementById('fullscreenGrid');
    const fullscreenModal = new bootstrap.Modal(document.getElementById('fullscreenModal'));
    const zoomLevelElement = document.getElementById('zoomLevel');

    let imgNaturalW = 1, imgNaturalH = 1;
    let baseBgW = 0, baseBgH = 0;
    let scaleFactor = 1;
    const MIN_SCALE = 0.3;
    const MAX_SCALE = 8;

    let bgW = 0, bgH = 0;
    let posX = 0, posY = 0;
    let dragging = false;
    let startClientX = 0, startClientY = 0;
    let startPosX = 0, startPosY = 0;

    // === GRID CONFIGURAÇÃO ===
    const rows = 80;
    const cols = 80;
    const gridData = Array.from({ length: rows }, () => Array(cols).fill(null));

    // === VARIÁVEIS PARA SELEÇÃO ESTILO EXCEL ===
    let isSelecting = false;
    let selectionStart = null;
    let currentSelection = null;

    // === CARREGAR IMAGEM ===
    const img = new Image();
    img.src = imageUrl + '?v=' + Date.now();
    
    img.onload = function() {
        console.log('Imagem carregada:', img.src);
        console.log('Dimensões naturais:', img.naturalWidth, 'x', img.naturalHeight);
        
        imgNaturalW = img.naturalWidth;
        imgNaturalH = img.naturalHeight;
        
        // Definir a imagem de fundo
        viewer.style.backgroundImage = `url('${img.src}')`;
        fullscreenViewer.style.backgroundImage = `url('${img.src}')`;
        
        initSizesAndPosition();
        createGrid();
        setupFullscreenGrid();
    };

    img.onerror = function() {
        console.error('Erro ao carregar imagem:', imageUrl);
        alert('Erro ao carregar a imagem. Verifique o caminho: ' + imageUrl);
    };

    // === INICIALIZAÇÃO COM ZOOM PARA PREENCHER A VIEWPORT ===
    function initSizesAndPosition() {
        const cw = viewer.clientWidth;
        const ch = viewer.clientHeight;
        
        console.log('Viewport dimensions:', cw, 'x', ch);
        console.log('Image dimensions:', imgNaturalW, 'x', imgNaturalH);
        
        // Calcula escala para preencher a viewport (cover)
        const scaleX = cw / imgNaturalW;
        const scaleY = ch / imgNaturalH;
        
        // Usa a maior escala para preencher a viewport completamente
        const initialScale = Math.max(scaleX, scaleY) * 1.0; // 100% do cover
        
        baseBgW = imgNaturalW;
        baseBgH = imgNaturalH;
        scaleFactor = initialScale;
        bgW = baseBgW * scaleFactor;
        bgH = baseBgH * scaleFactor;
        
        // Centraliza a imagem
        posX = (cw - bgW) / 2;
        posY = (ch - bgH) / 2;
        
        console.log('Initial scale:', scaleFactor);
        console.log('Scaled dimensions:', bgW, 'x', bgH);
        console.log('Position:', posX, posY);
        
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

        // Se houver seleção ativa, recalcula o retângulo visível para manter sincronização
        if (isSelecting || currentSelection) {
            updateSelectionRect();
        }
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
        const zoomSpeed = 1.1;
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
                
                // Apply sector color if exists
                if (gridData[r][c]) {
                    cell.style.backgroundColor = getSectorColorByName(gridData[r][c]);
                }
                
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

    // Update the updateFullscreenGrid function
    function updateFullscreenGrid() {
        if (!fullscreenModal._element.classList.contains('show')) return;
        
        const cells = fullscreenGrid.querySelectorAll('.grid-cell-fullscreen');
        cells.forEach((cell, index) => {
            const r = Math.floor(index / cols);
            const c = index % cols;
            
            if (gridData[r][c]) {
                const color = getSectorColorByName(gridData[r][c]);
                cell.style.backgroundColor = color;
            } else {
                cell.style.backgroundColor = 'transparent';
            }
        });
    }

    function getSectorColorByName(sectorName) {
        const sectorCard = Array.from(document.querySelectorAll('#setoresContainer div'))
            .find(card => card.textContent === sectorName);
        return sectorCard ? sectorCard.style.backgroundColor : 'rgba(0,0,0,0.5)';
    }

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
        }
    });

    window.addEventListener('mouseup', e => {
        if (e.button === 1 && dragging) {
            dragging = false;
            viewer.style.cursor = 'crosshair';
        }
    });

    // === CRIAÇÃO DO GRID ===
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
                    border: '1px solid rgba(0,132,255,0.1)',
                    backgroundColor: 'transparent',
                    pointerEvents: 'auto'
                });
                
                // Event listeners para seleção
                cell.addEventListener('mousedown', handleSelectionStart);
                
                gridOverlay.appendChild(cell);
            }
        }
        updateGrid();
    }

    function updateGrid() {
        const cellW = baseBgW / cols;
        const cellH = baseBgH / rows;
        const cells = gridOverlay.children;
        
        for (let i = 0; i < cells.length; i++) {
            const cell = cells[i];
            const r = parseInt(cell.dataset.row);
            const c = parseInt(cell.dataset.col);
            cell.style.width = `${cellW}px`;
            cell.style.height = `${cellH}px`;
            cell.style.left = `${c * cellW}px`;
            cell.style.top = `${r * cellH}px`;
        }
    }

    // === SELEÇÃO ESTILO EXCEL - CÁLCULO CORRIGIDO ===
    function handleSelectionStart(e) {
        if (e.button !== 0 || (!activeSector && !eraserMode)) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        isSelecting = true;
        const cell = e.target.closest('.grid-cell');
        const row = parseInt(cell.dataset.row);
        const col = parseInt(cell.dataset.col);
        
        selectionStart = { row, col };
        currentSelection = { 
            startRow: row, 
            startCol: col, 
            endRow: row, 
            endCol: col 
        };
        
        updateSelectionRect();
        selectionRect.style.display = 'block';
        
        // Previne seleção de texto durante o drag
        viewer.style.userSelect = 'none';
        
        // Adiciona event listeners para movimento e fim
        document.addEventListener('mousemove', handleSelectionMove);
        document.addEventListener('mouseup', handleSelectionEnd);
    }

    function handleSelectionMove(e) {
        if (!isSelecting) return;
        
        // Obtém a posição do grid overlay no viewport
        const gridRect = gridOverlay.getBoundingClientRect();
        
        // Calcula as coordenadas do mouse RELATIVAS ao grid overlay
        const mouseX = e.clientX - gridRect.left;
        const mouseY = e.clientY - gridRect.top;
        
        // Tamanho base das células (sem zoom)
        const baseCellW = baseBgW / cols;
        const baseCellH = baseBgH / rows;
        
        // Calcula a posição no grid
        const gridX = mouseX / scaleFactor;
        const gridY = mouseY / scaleFactor;
        
        const col = Math.floor(gridX / baseCellW);
        const row = Math.floor(gridY / baseCellH);
        
        // Limita às dimensões do grid
        const boundedCol = Math.max(0, Math.min(cols - 1, col));
        const boundedRow = Math.max(0, Math.min(rows - 1, row));
        
        if (currentSelection.endRow !== boundedRow || currentSelection.endCol !== boundedCol) {
            currentSelection.endRow = boundedRow;
            currentSelection.endCol = boundedCol;
            updateSelectionRect();
        }
    }

    function handleSelectionEnd(e) {
        if (!isSelecting) return;
        
        isSelecting = false;
        selectionRect.style.display = 'none';
        viewer.style.userSelect = 'auto';
        
        // Remove event listeners
        document.removeEventListener('mousemove', handleSelectionMove);
        document.removeEventListener('mouseup', handleSelectionEnd);
        
        // Aplica a cor aos cells selecionados
        applyColorToSelection();
        
        selectionStart = null;
        currentSelection = null;
    }

    function updateSelectionRect() {
        if (!currentSelection) {
            selectionRect.style.display = 'none';
            return;
        }
        
        // base cell size (não escalado)
        const cellW = baseBgW / cols;
        const cellH = baseBgH / rows;
        
        const startRow = Math.min(currentSelection.startRow, currentSelection.endRow);
        const endRow = Math.max(currentSelection.startRow, currentSelection.endRow);
        const startCol = Math.min(currentSelection.startCol, currentSelection.endCol);
        const endCol = Math.max(currentSelection.startCol, currentSelection.endCol);
        
        const left = startCol * cellW;
        const top = startRow * cellH;
        const width = (endCol - startCol + 1) * cellW;
        const height = (endRow - startRow + 1) * cellH;
        
        // Calcular valores em coordenadas de tela (considera scaleFactor e deslocamento posX/posY)
        const scaledLeft = posX + left * scaleFactor;
        const scaledTop = posY + top * scaleFactor;
        const scaledWidth = width * scaleFactor;
        const scaledHeight = height * scaleFactor;
        
        // Aplicar diretamente valores escalados e garantir que transform não interfira
        selectionRect.style.display = 'block';
        selectionRect.style.transform = 'none';
        selectionRect.style.left = `${scaledLeft}px`;
        selectionRect.style.top = `${scaledTop}px`;
        selectionRect.style.width = `${scaledWidth}px`;
        selectionRect.style.height = `${scaledHeight}px`;
    }

    function applyColorToSelection() {
        if (!currentSelection) return;
        
        const startRow = Math.min(currentSelection.startRow, currentSelection.endRow);
        const endRow = Math.max(currentSelection.startRow, currentSelection.endRow);
        const startCol = Math.min(currentSelection.startCol, currentSelection.endCol);
        const endCol = Math.max(currentSelection.startCol, currentSelection.endCol);
        
        for (let r = startRow; r <= endRow; r++) {
            for (let c = startCol; c <= endCol; c++) {
                if (eraserMode) {
                    gridData[r][c] = null;
                    const cell = getCellAt(r, c);
                    if (cell) cell.style.backgroundColor = 'transparent';
                } else if (activeSector) {
                    gridData[r][c] = activeSector.name;
                    const cell = getCellAt(r, c);
                    if (cell) cell.style.backgroundColor = activeSector.color;
                }
            }
        }
        
        // Atualiza o grid em tela cheia se estiver visível
        if (fullscreenModal._element.classList.contains('show')) {
            updateFullscreenGrid();
        }
    }

    function getCellAt(row, col) {
        return gridOverlay.querySelector(`.grid-cell[data-row="${row}"][data-col="${col}"]`);
    }

    // === PINTURA ===
    let activeSector = null;
    let eraserMode = false;

    // === GERAR SETORES ===
    const cores = [
        'rgba(255,99,132,0.5)','rgba(54,162,235,0.5)','rgba(255,206,86,0.5)',
        'rgba(75,192,192,0.5)','rgba(153,102,255,0.5)','rgba(255,159,64,0.5)',
        'rgba(199,199,199,0.5)','rgba(255,99,255,0.5)','rgba(99,255,132,0.5)',
        'rgba(255,220,99,0.5)'
    ];

    const setoresContainer = document.getElementById('setoresContainer');
    document.getElementById('btnGerar').addEventListener('click', () => {
        const num = parseInt(document.getElementById('numSetores').value);
        if(isNaN(num)||num<1||num>10) return alert('Digite entre 1 e 10 setores.');
        setoresContainer.innerHTML='';
        activeSector=null;

        for(let i=0;i<num;i++){
            const card=document.createElement('div');
            card.classList.add('p-3','rounded','text-center','text-white','shadow-sm');
            Object.assign(card.style,{
                width:'80px',height:'80px',backgroundColor:cores[i],
                display:'flex',justifyContent:'center',alignItems:'center',cursor:'pointer'
            });
            const setorName = `Setor ${String.fromCharCode(65+i)}`;
            card.textContent = setorName;
            card.addEventListener('click', ()=> {
                if(activeSector && activeSector.name===setorName){
                    activeSector=null;
                    card.style.outline='';
                    card.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
                } else {
                    activeSector={
                        name: setorName,
                        color: cores[i]
                    };
                    eraserMode=false;
                    document.querySelectorAll('#setoresContainer div').forEach(c=>{
                        c.style.outline='';
                        c.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
                    });
                    card.style.outline='3px solid #000';
                    card.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
                    document.getElementById('btnBorracha').classList.remove('btn-warning');
                    document.getElementById('btnBorracha').classList.add('btn-outline-warning');
                }
            });
            setoresContainer.appendChild(card);
        }
    });

    // === CONTROLES ===
    document.getElementById('btnLimpar').addEventListener('click', ()=>{
        gridOverlay.querySelectorAll('.grid-cell').forEach(cell=>cell.style.backgroundColor='transparent');
        for(let r=0;r<rows;r++) for(let c=0;c<cols;c++) gridData[r][c]=null;
        
        // Atualiza o grid em tela cheia se estiver visível
        if (fullscreenModal._element.classList.contains('show')) {
            updateFullscreenGrid();
        }
    });

    document.getElementById('btnBorracha').addEventListener('click', ()=>{
        eraserMode=!eraserMode;
        activeSector = null;
        const btn=document.getElementById('btnBorracha');
        btn.classList.toggle('btn-warning',eraserMode);
        btn.classList.toggle('btn-outline-warning',!eraserMode);
        
        // Remove outline dos setores
        document.querySelectorAll('#setoresContainer div').forEach(c=>{
            c.style.outline='';
            c.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
        });
    });

    // === SALVAR ===
    document.getElementById('btnSalvar').addEventListener('click', async () => {
        const setoresMap = {};

        // Coletar dados do grid
        for (let r = 0; r < gridData.length; r++) {
            for (let c = 0; c < gridData[r].length; c++) {
                const setor = gridData[r][c];
                if (!setor) continue;
                if (!setoresMap[setor]) {
                    setoresMap[setor] = {
                        nomeSetor: setor,
                        corSetor: getSectorColor(setor),
                        coordenadas: []
                    };
                }
                setoresMap[setor].coordenadas.push({ x: c, y: r });
            }
        }

        const setoresArray = Object.values(setoresMap).flatMap(s =>
            s.coordenadas.map(coord => ({
                nomeSetor: s.nomeSetor,
                corSetor: s.corSetor,
                x: coord.x,
                y: coord.y
            }))
        );

        if (setoresArray.length === 0) {
            alert('⚠️ Nenhum setor definido!');
            return;
        }

        const payload = {
            idProjeto: @json($projeto->idProjeto),
            setores: setoresArray
        };

        console.log('Enviando dados:', payload);

        try {
            const response = await fetch("{{ route('setores.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            console.log('Resposta do servidor:', result);
            
            if (result.success) {
                alert('✅ Setores salvos com sucesso!');
                if (result.idProjeto) {
                    window.location.href = `/vagas/nova/${result.idProjeto}`;
                }
            } else {
                alert('⚠️ Erro ao salvar: ' + (result.message || 'Erro desconhecido'));
            }
        } catch (err) {
            console.error('Erro na requisição:', err);
            alert('❌ Falha ao comunicar com o servidor. Verifique o console para detalhes.');
        }
    });

    function getSectorColor(nomeSetor) {
        const card = Array.from(document.querySelectorAll('#setoresContainer div'))
            .find(c => c.textContent === nomeSetor);
        if (card) {
            return card.style.backgroundColor;
        }
        return 'rgba(0,0,0,0.5)';
    }

    // === REDIMENSIONAMENTO DA JANELA ===
    window.addEventListener('resize', () => {
        initSizesAndPosition();
    });

    // Inicialização quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM carregado, iniciando aplicação...');
    });

})();
</script>
@endsection