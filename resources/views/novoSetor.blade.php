@extends('layouts.app')

@section('title', 'Novo Setor')

@section('content')
<div class="container-fluid p-0" style="height:100vh;">
    <div class="row h-100">

        <!-- === VIEWPORT === -->
        <div class="col-8 d-flex justify-content-center align-items-center" 
             style="background-color:#f0f0f0; border-right:1px solid #ccc; overflow:hidden; position:relative;">
            <div id="viewer" 
                 style="width:100%; height:100%; cursor:grab; user-select:none; -webkit-user-select:none; background-repeat:no-repeat; background-position:center center; position:relative;">
                <div id="grid-overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;">
                    <!-- Grid gerado via JS -->
                </div>
            </div>
        </div>

        <!-- === SIDEBAR === -->
        <div class="col-4 py-4 d-flex flex-column align-items-center">
            <div style="width:90%;">

                <h4 class="text-center mb-3">Painel de Controle</h4>

                <!-- === TOOLBAR === -->
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

                <!-- === CONTROLES === -->
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <button id="btnLimpar" class="btn btn-outline-danger btn-sm">🧹 Limpar Setores</button>
                    <button id="btnBorracha" class="btn btn-outline-secondary btn-sm">🩹 Borracha</button>
                </div>

                <!-- === BOTÃO SALVAR === -->
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
    const imageUrl = @json(asset($caminhoPublico));
    const viewer = document.getElementById('viewer');
    const gridOverlay = document.getElementById('grid-overlay');

    let imgNaturalW = 1, imgNaturalH = 1;
    let baseBgW = 0, baseBgH = 0;
    let scaleFactor = 1;
    const MIN_SCALE_FACTOR = 1;
    const MAX_SCALE_FACTOR = 6;

    let bgW = 0, bgH = 0;
    let posX = 0, posY = 0;
    let dragging = false;
    let startClientX = 0, startClientY = 0;
    let startPosX = 0, startPosY = 0;

    const rows = 25;
    const cols = 25;
    const gridData = Array.from({ length: rows }, () => Array(cols).fill(null));

    const img = new Image();
    img.src = imageUrl;
    img.onload = () => {
        imgNaturalW = img.naturalWidth;
        imgNaturalH = img.naturalHeight;
        viewer.style.backgroundImage = `url(${imageUrl})`;
        initSizesAndPosition();
        createGrid();
    };

    function initSizesAndPosition() {
        const containerW = viewer.clientWidth;
        const containerH = viewer.clientHeight;
        const baseScale = Math.max(containerW / imgNaturalW, containerH / imgNaturalH);

        baseBgW = imgNaturalW * baseScale;
        baseBgH = imgNaturalH * baseScale;

        scaleFactor = 1;
        bgW = baseBgW;
        bgH = baseBgH;

        posX = (containerW - bgW) / 2;
        posY = (containerH - bgH) / 2;

        applyTransform();
        updateGrid();
    }

    function applyTransform() {
        viewer.style.backgroundSize = `${bgW}px ${bgH}px`;
        viewer.style.backgroundPosition = `${posX}px ${posY}px`;
        gridOverlay.style.transform = `translate(${posX}px, ${posY}px) scale(${bgW / baseBgW}, ${bgH / baseBgH})`;
        gridOverlay.style.transformOrigin = 'top left';
    }

    function clampPosition() {
        const containerW = viewer.clientWidth;
        const containerH = viewer.clientHeight;
        posX = Math.min(0, Math.max(containerW - bgW, posX));
        posY = Math.min(0, Math.max(containerH - bgH, posY));
    }

    // === ZOOM ===
    viewer.addEventListener('wheel', e => {
        e.preventDefault();
        const zoomSpeed = 1.1;
        const delta = e.deltaY < 0 ? zoomSpeed : 1 / zoomSpeed;
        const newScale = Math.min(MAX_SCALE_FACTOR, Math.max(MIN_SCALE_FACTOR, scaleFactor * delta));

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
    });

    // === ARRASTO COM BOTÃO DO MEIO ===
    viewer.addEventListener('mousedown', e => {
        if (e.button !== 1) return;
        e.preventDefault();
        dragging = true;
        startClientX = e.clientX;
        startClientY = e.clientY;
        startPosX = posX;
        startPosY = posY;
        viewer.style.cursor = 'grabbing';
    });
    window.addEventListener('mousemove', e => {
        if (!dragging) return;
        posX = startPosX + (e.clientX - startClientX);
        posY = startPosY + (e.clientY - startClientY);
        clampPosition();
        applyTransform();
    });
    window.addEventListener('mouseup', e => {
        if (e.button === 1) {
            dragging = false;
            viewer.style.cursor = 'grab';
        }
    });

    // === GRID ===
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
                    border: '1px solid rgba(0,132,255,0.25)',
                    backgroundColor: 'rgba(0,132,255,0.08)',
                    pointerEvents: 'auto'
                });
                cell.addEventListener('mousedown', handleCellPaint);
                cell.addEventListener('mouseenter', handleCellHover);
                cell.addEventListener('dblclick', handleCellClear);
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

    // === PINTURA ===
    let activeSector = null;
    let isPainting = false;
    let eraserMode = false;

    function handleCellPaint(e){ e.preventDefault(); isPainting = true; paintCell(e.target);}
    function handleCellHover(e){ if(isPainting) paintCell(e.target);}
    window.addEventListener('mouseup', () => isPainting=false);

    function paintCell(cell){
        const r = +cell.dataset.row;
        const c = +cell.dataset.col;
        if(eraserMode){
            gridData[r][c] = null;
            cell.style.backgroundColor='rgba(0,132,255,0.08)';
        } else if(activeSector){
            gridData[r][c] = activeSector.name;
            cell.style.backgroundColor = activeSector.color;
        }
    }

    function handleCellClear(e){
        const r = +e.target.dataset.row;
        const c = +e.target.dataset.col;
        gridData[r][c] = null;
        e.target.style.backgroundColor='rgba(0,132,255,0.08)';
    }

    // === SETORES (TOGGLE) ===
    const cores = [
        'rgba(255, 99, 132, 0.5)','rgba(54, 162, 235, 0.5)','rgba(255, 206, 86, 0.5)',
        'rgba(75, 192, 192, 0.5)','rgba(153, 102, 255, 0.5)','rgba(255, 159, 64, 0.5)',
        'rgba(199, 199, 199, 0.5)','rgba(255, 99, 255, 0.5)','rgba(99, 255, 132, 0.5)',
        'rgba(255, 220, 99, 0.5)'
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
            Object.assign(card.style,{width:'80px',height:'80px',backgroundColor:cores[i],display:'flex',justifyContent:'center',alignItems:'center',cursor:'pointer'});
            card.textContent=`Setor ${String.fromCharCode(65+i)}`;

            card.addEventListener('click', ()=> {
                if(activeSector && activeSector.name===card.textContent){
                    activeSector=null;
                    card.style.outline='';
                } else {
                    activeSector={name:card.textContent,color:cores[i]};
                    eraserMode=false;
                    document.querySelectorAll('#setoresContainer div').forEach(c=>c.style.outline='');
                    card.style.outline='3px solid #000';
                }
            });
            setoresContainer.appendChild(card);
        }
    });

    // === CONTROLES ===
    document.getElementById('btnLimpar').addEventListener('click', ()=>{
        gridOverlay.querySelectorAll('.grid-cell').forEach(cell=>cell.style.backgroundColor='rgba(0,132,255,0.08)');
        for(let r=0;r<rows;r++) for(let c=0;c<cols;c++) gridData[r][c]=null;
    });

    document.getElementById('btnBorracha').addEventListener('click', ()=>{
        eraserMode=!eraserMode;
        document.getElementById('btnBorracha').classList.toggle('btn-secondary',eraserMode);
        document.getElementById('btnBorracha').classList.toggle('btn-outline-secondary',!eraserMode);
    });

    // === SALVAR ===
    document.getElementById('btnSalvar').addEventListener('click', async () => {
        const setoresMap = {};

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

        if (setoresArray.length === 0) return alert('Nenhum setor definido!');

        const payload = {
            idProjeto: @json($projeto->idProjeto),
            setores: setoresArray
        };

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
            if (result.success) alert('✅ Setores salvos com sucesso!');
            else alert('⚠️ Erro ao salvar.');
        } catch (err) {
            console.error(err);
            alert('❌ Falha ao comunicar com o servidor.');
        }
    });

    function getSectorColor(nomeSetor) {
        const card = Array.from(document.querySelectorAll('#setoresContainer div'))
            .find(c => c.textContent === nomeSetor);
        return card ? card.style.backgroundColor : 'rgba(0,0,0,0.5)';
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

        for(const setor in setoresMap){
            resumoBody.innerHTML+=`<tr><td>${setor}</td><td>${setoresMap[setor]}</td></tr>`;
        }

        resumoContainer.style.display='block';
    });

})();
</script>
@endsection