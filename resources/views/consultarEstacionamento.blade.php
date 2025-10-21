@extends('layouts.app')

@section('title', 'Consultar Estacionamento')

@section('content')
<div class="container-fluid p-0" style="height:100vh;">
    <div class="row h-100">

        <!-- VIEWPORT -->
        <div class="col-9 d-flex justify-content-center align-items-center"
             style="background-color:#f8f9fa; border-right:1px solid #ddd; overflow:hidden; position:relative;">
            <div id="viewer" style="width:100%; height:100%; background-repeat:no-repeat; background-position:center center; position:relative;">
                <div id="grid-overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
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

<script>
(async function(){
    // projetos vindo do controller
    @php
        $projetosJs = $projetos->map(function($p){
            return [
                'id' => $p->idProjeto,
                'nome' => $p->nomeProjeto,
                // garante URL absoluta correta para a imagem (usa asset('storage/...'))
                'caminhoUrl' => asset('storage/' . ltrim($p->caminhoPlantaEstacionamento ?? '', '/')),
                'caminhoRaw' => $p->caminhoPlantaEstacionamento,
            ];
        })->toArray();
    @endphp
    const projetos = @json($projetosJs);
    // storagePrefix não é mais necessário quando usamos caminhoUrl pronto
    const storagePrefix = '';
    const viewer = document.getElementById('viewer');
    const gridOverlay = document.getElementById('grid-overlay');
    const legendCard = document.getElementById('legendaSetoresCard');
    const legendContainer = document.getElementById('legendaSetores');

    let imgNaturalW = 1, imgNaturalH = 1;
    let baseBgW = 0, baseBgH = 0;
    let bgW = 0, bgH = 0, posX = 0, posY = 0, scaleFactor = 1;
    const rows = 80, cols = 80;
    const tipoColors = { carro: 'rgba(0,123,255,0.6)', moto: 'rgba(40,167,69,0.6)', idoso:'rgba(255,193,7,0.6)', deficiente:'rgba(108,117,125,0.6)' };
    const sectorGrid = Array.from({ length: rows }, () => Array(cols).fill(null));
    let setorColors = {};

    // initial imageUrl based on selected project or first
    function projectById(id){ return projetos.find(p=>p.id==id) ?? null; }

    // init grid
    function createGrid(){
        gridOverlay.innerHTML = '';
        for(let r=0;r<rows;r++){
            for(let c=0;c<cols;c++){
                const cell = document.createElement('div');
                cell.className = 'grid-cell';
                cell.dataset.row = r; cell.dataset.col = c;
                Object.assign(cell.style, { position:'absolute', border:'1px solid rgba(0,0,0,0.03)', boxSizing:'border-box', pointerEvents:'none' });
                gridOverlay.appendChild(cell);
            }
        }
        updateGrid();
    }

    function updateGrid(){
        const cellW = baseBgW / cols, cellH = baseBgH / rows;
        for(const cell of gridOverlay.children){
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
    }

    function applyTransform(){
        viewer.style.backgroundSize = `${bgW}px ${bgH}px`;
        viewer.style.backgroundPosition = `${posX}px ${posY}px`;
        gridOverlay.style.transform = `translate(${posX}px, ${posY}px) scale(${bgW / baseBgW})`;
        gridOverlay.style.transformOrigin = 'top left';
    }

    function renderAll(){
        // pinta setor de fundo
        for(let r=0;r<rows;r++){
            for(let c=0;c<cols;c++){
                const cell = gridOverlay.querySelector(`.grid-cell[data-row='${r}'][data-col='${c}']`);
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
            renderAll();
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

    // load vagas and paint them on top
    async function loadVagas(idProjeto){
        try {
            const res = await fetch(`/vagas/listar/${idProjeto}`);
            const vagas = await res.json();
            // repaint background first
            renderAll();
            vagas.forEach(v=>{
                const color = tipoColors[v.tipoVaga] || tipoColors.carro;
                (v.grids||[]).forEach(g=>{
                    const r = Number(g.posicaoVagaY), c = Number(g.posicaoVagaX);
                    const cell = gridOverlay.querySelector(`.grid-cell[data-row='${r}'][data-col='${c}']`);
                    if(cell) cell.style.backgroundColor = color;
                });
            });
        } catch(e){
            console.error('Erro ao carregar vagas:', e);
        }
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
            gridOverlay.innerHTML = '';
            console.error('Não foi possível carregar a planta:', src);
        };
    }

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

    window.addEventListener('resize', ()=> { initSizes(); });

})();
</script>

<style>
.grid-cell { transition: background-color .06s linear; }
</style>
@endsection