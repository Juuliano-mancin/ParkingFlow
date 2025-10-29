@extends('layouts.app')

@section('title', 'Novo Setor')

@section('content')
<div class="container-fluid p-0" style="height:100vh;">
    <div class="row h-100">

        <!-- === VIEWPORT PRINCIPAL === -->
        <div class="col-8 d-flex justify-content-center align-items-center"
             style="background-color:#f0f0f0; border-right:1px solid #ccc; overflow:hidden; position:relative;">
            <div id="viewer"
                 style="width:100%; height:100%; cursor:crosshair; user-select:none; background-repeat:no-repeat; background-position:center center; position:relative;">
                <div id="grid-overlay"
                     style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;">
                </div>
                <!-- Elemento para seleção estilo Excel -->
                <div id="selection-rect" style="position:absolute; border:2px dashed #007bff; background-color:rgba(0,123,255,0.1); pointer-events:none; display:none; z-index:1000;"></div>
            </div>
        </div>

        <!-- === SIDEBAR CONTROLES === -->
        <div class="col-4 py-4 d-flex flex-column align-items-center overflow-auto">
            <div style="width:90%;">

                <h4 class="text-center mb-3">Painel de Controle</h4>

                <!-- === GERAR SETORES === -->
                <div class="card p-3 mb-4 shadow-sm">
                    <h5 class="mb-3 text-center">Gerar Setores</h5>
                    <div class="input-group mb-3">
                        <input type="number" id="numSetores" class="form-control" placeholder="Qtd. de setores" min="1" max="10">
                        <button id="btnGerar" class="btn btn-primary">Gerar</button>
                    </div>
                    <div class="text-center mt-2">
                        <button id="btnResumo" class="btn btn-info btn-sm">📊 Detalhes do Estacionamento</button>
                    </div>
                    <small class="text-muted d-block text-center">Máx: 10 setores</small>
                </div>

                <!-- === LISTA DE SETORES === -->
                <div id="setoresContainer" class="d-flex flex-wrap justify-content-center gap-2 mb-3"></div>

                <!-- === CONTROLES DE EDIÇÃO === -->
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <button id="btnLimpar" class="btn btn-outline-danger btn-sm">🧹 Limpar Setores</button>
                    <button id="btnBorracha" class="btn btn-outline-secondary btn-sm">🩹 Borracha</button>
                </div>

                <!-- === SALVAR === -->
                <div class="text-center mb-3">
                    <button id="btnSalvar" class="btn btn-success">💾 Salvar Setores</button>
                </div>

                <!-- === RESUMO === -->
                <div class="row mt-4" id="resumoContainer" style="display:none;">
                    <div class="col-12">
                        <div class="card p-3 shadow-sm">
                            <h5 class="mb-3 text-center">Resumo do Estacionamento</h5>
                            <table class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Setores</th>
                                        <th>Total Grids</th>
                                    </tr>
                                </thead>
                                <tbody id="resumoBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Voltar para Dashboard</a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // === VARIÁVEIS GERAIS ===
    const imageUrl = @json(asset($caminhoPublico));
    const viewer = document.getElementById('viewer');
    const gridOverlay = document.getElementById('grid-overlay');
    const selectionRect = document.getElementById('selection-rect');

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

    // === GRID CONFIGURAÇÃO ===
    const rows = 80;
    const cols = 80;
    const gridData = Array.from({ length: rows }, () => Array(cols).fill(null));

    // === VARIÁVEIS PARA SELEÇÃO ESTILO EXCEL ===
    let isSelecting = false;
    let selectionStart = null;
    let currentSelection = null;

    const img = new Image();
    img.src = imageUrl + '?v=' + Date.now();
    img.onload = () => {
        imgNaturalW = img.naturalWidth;
        imgNaturalH = img.naturalHeight;
        viewer.style.backgroundImage = `url(${img.src})`;
        initSizesAndPosition();
        createGrid();
    };

    // === AJUSTES DE TAMANHO ===
    function initSizesAndPosition() {
        const cw = viewer.clientWidth;
        const ch = viewer.clientHeight;
        const scale = Math.max(cw / imgNaturalW, ch / imgNaturalH);
        baseBgW = imgNaturalW * scale;
        baseBgH = imgNaturalH * scale;
        scaleFactor = 1;
        bgW = baseBgW;
        bgH = baseBgH;
        posX = (cw - bgW) / 2;
        posY = (ch - bgH) / 2;
        applyTransform();
        updateGrid();
    }

    function applyTransform() {
        viewer.style.backgroundSize = `${bgW}px ${bgH}px`;
        viewer.style.backgroundPosition = `${posX}px ${posY}px`;
        gridOverlay.style.transform = `translate(${posX}px, ${posY}px) scale(${bgW / baseBgW})`;
        gridOverlay.style.transformOrigin = 'top left';
        selectionRect.style.transform = `translate(${posX}px, ${posY}px) scale(${bgW / baseBgW})`;
        selectionRect.style.transformOrigin = 'top left';
    }

    function clampPosition() {
        const cw = viewer.clientWidth;
        const ch = viewer.clientHeight;
        posX = Math.min(0, Math.max(cw - bgW, posX));
        posY = Math.min(0, Math.max(ch - bgH, posY));
    }

    // === ZOOM ===
    viewer.addEventListener('wheel', e => {
        e.preventDefault();
        const zoomSpeed = 1.1;
        const delta = e.deltaY < 0 ? zoomSpeed : 1 / zoomSpeed;
        const newScale = Math.min(MAX_SCALE, Math.max(MIN_SCALE, scaleFactor * delta));

        const rect = viewer.getBoundingClientRect();
        const mx = e.clientX - rect.left;
        const my = e.clientY - rect.top;
        const relX = (mx - posX) / bgW;
        const relY = (my - posY) / bgH;

        bgW = baseBgW * newScale;
        bgH = baseBgH * newScale;
        scaleFactor = newScale;

        posX = mx - relX * bgW;
        posY = my - relY * bgH;

        clampPosition();
        applyTransform();
        updateGrid();
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
        }
        
        // Seleção com left mouse button
        if (isSelecting) {
            handleSelectionMove(e);
        }
    });

    window.addEventListener('mouseup', e => {
        if (e.button === 1 && dragging) {
            dragging = false;
            viewer.style.cursor = 'crosshair';
        }
        
        if (e.button === 0 && isSelecting) {
            handleSelectionEnd(e);
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
        for (const cell of gridOverlay.children) {
            const r = +cell.dataset.row;
            const c = +cell.dataset.col;
            cell.style.width = `${cellW}px`;
            cell.style.height = `${cellH}px`;
            cell.style.left = `${c * cellW}px`;
            cell.style.top = `${r * cellH}px`;
        }
    }

    // === SELEÇÃO ESTILO EXCEL ===
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
    }

    function handleSelectionMove(e) {
        if (!isSelecting) return;
        
        const cell = e.target.closest('.grid-cell');
        if (cell) {
            const row = parseInt(cell.dataset.row);
            const col = parseInt(cell.dataset.col);
            
            // Atualiza apenas se mudou de célula
            if (currentSelection.endRow !== row || currentSelection.endCol !== col) {
                currentSelection.endRow = row;
                currentSelection.endCol = col;
                updateSelectionRect();
            }
        } else {
            // Se não está sobre uma célula, calcula baseado na posição do mouse
            const rect = gridOverlay.getBoundingClientRect();
            const mouseX = e.clientX - rect.left;
            const mouseY = e.clientY - rect.top;
            
            const cellW = baseBgW / cols;
            const cellH = baseBgH / rows;
            
            const col = Math.floor(mouseX / cellW);
            const row = Math.floor(mouseY / cellH);
            
            if (col >= 0 && col < cols && row >= 0 && row < rows) {
                if (currentSelection.endRow !== row || currentSelection.endCol !== col) {
                    currentSelection.endRow = row;
                    currentSelection.endCol = col;
                    updateSelectionRect();
                }
            }
        }
    }

    function handleSelectionEnd(e) {
        if (!isSelecting) return;
        
        isSelecting = false;
        selectionRect.style.display = 'none';
        viewer.style.userSelect = 'auto';
        
        // Aplica a cor aos cells selecionados
        applyColorToSelection();
        
        selectionStart = null;
        currentSelection = null;
    }

    function updateSelectionRect() {
        if (!currentSelection) return;
        
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
        
        // Aplica a mesma transformação do grid overlay
        selectionRect.style.left = `${left}px`;
        selectionRect.style.top = `${top}px`;
        selectionRect.style.width = `${width}px`;
        selectionRect.style.height = `${height}px`;
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
            card.classList.add('p-3','rounded','text-center','text-white');
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
                } else {
                    activeSector={
                        name: setorName,
                        color: cores[i]
                    };
                    eraserMode=false;
                    document.querySelectorAll('#setoresContainer div').forEach(c=>c.style.outline='');
                    card.style.outline='3px solid #000';
                    document.getElementById('btnBorracha').classList.remove('btn-secondary');
                    document.getElementById('btnBorracha').classList.add('btn-outline-secondary');
                }
            });
            setoresContainer.appendChild(card);
        }
    });

    // === CONTROLES ===
    document.getElementById('btnLimpar').addEventListener('click', ()=>{
        gridOverlay.querySelectorAll('.grid-cell').forEach(cell=>cell.style.backgroundColor='transparent');
        for(let r=0;r<rows;r++) for(let c=0;c<cols;c++) gridData[r][c]=null;
    });

    document.getElementById('btnBorracha').addEventListener('click', ()=>{
        eraserMode=!eraserMode;
        activeSector = null;
        const btn=document.getElementById('btnBorracha');
        btn.classList.toggle('btn-secondary',eraserMode);
        btn.classList.toggle('btn-outline-secondary',!eraserMode);
        
        // Remove outline dos setores
        document.querySelectorAll('#setoresContainer div').forEach(c=>c.style.outline='');
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

    // === RESUMO ===
    const btnResumo=document.getElementById('btnResumo');
    const resumoBody=document.getElementById('resumoBody');
    const resumoContainer=document.getElementById('resumoContainer');

    btnResumo.addEventListener('click', ()=>{
        resumoBody.innerHTML='';
        const setoresMap={};
        for(let r=0;r<gridData.length;r++){
            for(let c=0;c<gridData[0].length;c++){
                const setor=gridData[r][c];
                if(!setor) continue;
                if(!setoresMap[setor]) setoresMap[setor]=0;
                setoresMap[setor]++;
            }
        }
        
        if (Object.keys(setoresMap).length === 0) {
            resumoBody.innerHTML = '<tr><td colspan="2" class="text-center">Nenhum setor definido</td></tr>';
        } else {
            for(const setor in setoresMap){
                resumoBody.innerHTML+=`<tr><td>${setor}</td><td>${setoresMap[setor]}</td></tr>`;
            }
        }
        resumoContainer.style.display='block';
    });

})();
</script>
@endsection