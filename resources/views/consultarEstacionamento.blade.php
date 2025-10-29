@extends('layouts.app')

@section('title', 'Consultar Estacionamento')

@section('content')
<div class="container-fluid p-0" style="height:100vh;">
    <div class="row h-100">

        <!-- VIEWPORT -->
        <div class="col-9 d-flex justify-content-center align-items-center"
             style="background-color:#f8f9fa; border-right:1px solid #ddd; overflow:hidden; position:relative;">
            
            <!-- CONTROLES DE VISUALIZAÇÃO -->
            <div class="position-absolute top-0 start-0 m-3 z-3">
                <div class="btn-group shadow-sm">
                    <button id="toggleSetores" class="btn btn-primary active">
                        <i class="fas fa-layer-group"></i> Setores
                    </button>
                    <button id="toggleVagas" class="btn btn-success active">
                        <i class="fas fa-car"></i> Vagas
                    </button>
                </div>
            </div>

            <div id="viewer" style="width:100%; height:100%; background-repeat:no-repeat; background-position:center center; position:relative;">
                <!-- Camada de Setores (fundo) -->
                <div id="setores-layer" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
                
                <!-- Camada de Vagas (sobreposição) -->
                <div id="vagas-layer" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
            </div>
        </div>

        <!-- SIDEBAR -->
        <div class="col-3 py-4 d-flex flex-column align-items-center overflow-auto"
             style="background-color:#fff; position:relative;">

            <!-- PROJECT SELECT -->
            <div class="card p-3 mb-3 shadow-sm" style="width:90%;">
                <label for="selectProjeto" class="form-label">Projeto</label>
                <select id="selectProjeto" class="form-select">
                    @foreach($projetos as $p)
                        <option value="{{ $p->idProjeto }}"
                                data-caminho="{{ $p->caminhoPlantaEstacionamento }}"
                                {{ (isset($projeto) && $projeto->idProjeto === $p->idProjeto) ? 'selected' : '' }}>
                            {{ $p->nomeProjeto }}
                        </option>
                    @endforeach
                </select>
            </div>

            <h4 class="w-100 text-center mb-2">Legenda dos Setores</h4>
            <div id="legendaSetoresCard" class="card p-3 mb-3 shadow-sm" style="width:90%; display:none;">
                <div id="legendaSetores" class="d-flex flex-wrap gap-2 justify-content-center"></div>
            </div>

            <h4 class="w-100 text-center mb-2">Vagas por Tipo</h4>
            <div class="w-100 d-flex flex-column align-items-center mb-4" style="width:90%;">
                <div class="d-flex align-items-center" style="width:100%;">
                    <div class="vaga-indicador carro" style="width:20px; height:20px; background-color:rgba(0,123,255,0.6); border-radius:3px; margin-right:10px;"></div>
                    <div class="vaga-label" style="font-size:14px; color:#333;">Carro</div>
                </div>
                <div class="d-flex align-items-center" style="width:100%;">
                    <div class="vaga-indicador moto" style="width:20px; height:20px; background-color:rgba(40,167,69,0.6); border-radius:3px; margin-right:10px;"></div>
                    <div class="vaga-label" style="font-size:14px; color:#333;">Moto</div>
                </div>
                <div class="d-flex align-items-center" style="width:100%;">
                    <div class="vaga-indicador idoso" style="width:20px; height:20px; background-color:rgba(255,193,7,0.6); border-radius:3px; margin-right:10px;"></div>
                    <div class="vaga-label" style="font-size:14px; color:#333;">Idoso</div>
                </div>
                <div class="d-flex align-items-center" style="width:100%;">
                    <div class="vaga-indicador deficiente" style="width:20px; height:20px; background-color:rgba(108,117,125,0.6); border-radius:3px; margin-right:10px;"></div>
                    <div class="vaga-label" style="font-size:14px; color:#333;">Deficiente</div>
                </div>
            </div>

            <div class="text-center mt-4" style="width:90%;">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100">Voltar para Dashboard</a>
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

    let imgNaturalW = 1, imgNaturalH = 1;
    let baseBgW = 0, baseBgH = 0;
    let bgW = 0, bgH = 0, posX = 0, posY = 0, scaleFactor = 1;
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

    // Inicializar controles
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

    function initSizes(){
        const containerW = viewer.clientWidth, containerH = viewer.clientHeight;
        const baseScale = Math.max(containerW / imgNaturalW, containerH / imgNaturalH) || 1;
        baseBgW = imgNaturalW * baseScale; baseBgH = imgNaturalH * baseScale;
        bgW = baseBgW; bgH = baseBgH;
        posX = (containerW - bgW) / 2; posY = (containerH - bgH) / 2;
        applyTransform();
        updateGrid();
        renderVagas(); // Re-render vagas após resize
    }

    function applyTransform(){
        viewer.style.backgroundSize = `${bgW}px ${bgH}px`;
        viewer.style.backgroundPosition = `${posX}px ${posY}px`;
        setoresLayer.style.transform = `translate(${posX}px, ${posY}px) scale(${bgW / baseBgW})`;
        setoresLayer.style.transformOrigin = 'top left';
        vagasLayer.style.transform = `translate(${posX}px, ${posY}px) scale(${bgW / baseBgW})`;
        vagasLayer.style.transformOrigin = 'top left';
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
            el.className = 'd-flex align-items-center gap-2 p-1';
            el.style.minWidth = '100px';
            el.style.justifyContent = 'center';
            el.innerHTML = `<div style="width:18px;height:18px;border-radius:3px;background:${setorColors[nome]};"></div>
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
            initSizes();
            createGrid();
            loadSetores(idProjeto);
            loadVagas(idProjeto);
        };
        img.onerror = () => {
            viewer.style.backgroundImage = '';
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

    window.addEventListener('resize', ()=> { initSizes(); });

})();
</script>

<style>
.grid-cell { transition: background-color .06s linear; }
.vaga-icon { transition: all 0.2s ease; }
.vaga-icon:hover { transform: scale(1.1); }
.vaga-border { transition: all 0.2s ease; }
.btn-group .btn { border-radius: 0.375rem !important; margin: 0 2px; }
</style>
@endsection