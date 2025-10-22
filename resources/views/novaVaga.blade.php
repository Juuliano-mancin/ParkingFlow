@extends('layouts.app')

@section('title', 'Cadastro de Vagas')

@section('content')
<div class="container-fluid p-0" style="height:100vh;">
    <div class="row h-100">

        <!-- === VIEWPORT === -->
        <div class="col-8 d-flex justify-content-center align-items-center"
             style="background-color:#f0f0f0; border-right:1px solid #ccc; overflow:hidden; position:relative;">
            <div id="viewer"
                 style="width:100%; height:100%; cursor:grab; user-select:none; -webkit-user-select:none; background-repeat:no-repeat; background-position:center center; position:relative;">
                <!-- gridOverlay contém as células responsivas alinhadas à imagem (transform aplicada para zoom/pan) -->
                <div id="grid-overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
            </div>
        </div>

        <!-- === SIDEBAR === -->
        <div class="col-4 py-4 d-flex flex-column align-items-center overflow-auto">
            <div style="width:90%;">

                <h4 class="text-center mb-3">Painel de Controle</h4>

                <!-- === LEGENDA DOS SETORES (apenas visual) === -->
                <div class="card p-3 mb-4 shadow-sm" id="legendaSetoresCard" style="display:none;">
                    <h5 class="mb-3 text-center">Legenda dos Setores</h5>
                    <div id="legendaSetores" class="d-flex flex-wrap gap-2 justify-content-center"></div>
                </div>

                <!-- === SELECTOR DE SETOR (usar idSetor ao criar vagas) -->
                <div class="card p-3 mb-4 shadow-sm">
                    <h5 class="mb-2 text-center">Setor atual</h5>
                    <select id="selectSetor" class="form-select">
                        @foreach($setores as $s)
                            <option value="{{ $s->idSetor }}">{{ $s->nomeSetor }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- === LISTA DE TIPOS (toolbar) === -->
                <div class="card p-3 mb-4 shadow-sm">
                    <h5 class="mb-2 text-center">Tipos de Vaga</h5>
                    <div id="tiposToolbar" class="d-flex gap-2 justify-content-center"></div>
                </div>

                <!-- === INFORMAÇÃO DE VAGAS TEMPORÁRIAS === -->
                <div class="card p-3 mb-4 shadow-sm">
                    <h6 class="mb-2 text-center">Vagas (temporárias)</h6>
                    <div id="vagasList" style="min-height:40px; max-height:160px; overflow:auto;"></div>
                </div>

                <!-- === CONTROLES === -->
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <button id="btnLimpar" class="btn btn-outline-danger btn-sm">🧹 Limpar Vagas</button>
                    <button id="btnBorracha" class="btn btn-outline-secondary btn-sm">🩹 Borracha</button>
                </div>

                <div class="text-center mb-3">
                    <button id="btnSalvar" class="btn btn-success">💾 Salvar Vagas</button>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Voltar para Dashboard</a>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
/*
  novaVaga.blade.php
  - Grid sobre a planta
  - Seleção retangular de células (tipo Excel) cria vagas temporárias (array `vagas`)
  - Toolbar com tipos: carro, moto, idoso, deficiente (aplica tipo para próxima seleção)
  - Borracha remove vaga inteira ao clicar em qualquer célula da vaga
  - Legenda de setores apenas visual (carregada via API /api/setores/{idProjeto})
  - Zoom (wheel) e pan (botão do meio) funcionais
*/

(async function () {
    // URL da planta (Blade)
    const imageUrl = @json(asset('storage/' . $projeto->caminhoPlantaEstacionamento));
    const viewer = document.getElementById('viewer');
    const gridOverlay = document.getElementById('grid-overlay');

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
    const gridData = Array.from({ length: rows }, () => Array(cols).fill(null)); // usada para armazenar setores de fundo (se houver)

    // Sector grid visual (cores do setor por célula) - opcional se API fornecer coordenadas
    const sectorGrid = Array.from({ length: rows }, () => Array(cols).fill(null));
    let setorColors = {}; // mapa nomeSetor -> cor (para legenda)
    let sectoresList = []; // dados crus vindos da API

    // Vagas temporárias: array com objetos {nomeVaga, tipoVaga, posicoes:[{x,y}], id:number}
    let vagas = [];
    let vagaCounter = 0;

    // Tipos de vaga e cores (com transparência)
    const tipoColors = {
        carro: 'rgba(0,123,255,0.6)',
        moto: 'rgba(40,167,69,0.6)',
        idoso: 'rgba(255,193,7,0.6)',
        deficiente: 'rgba(108,117,125,0.6)'
    };
    let currentTipo = 'carro';

    // selecionador de setor (inicializado a partir do <select> blade)
    let selectedSetorId = null;
    const selectSetorEl = document.getElementById('selectSetor');
    if (selectSetorEl) {
        selectedSetorId = Number(selectSetorEl.value);
        selectSetorEl.addEventListener('change', () => {
            selectedSetorId = Number(selectSetorEl.value);
        });
    }

    // Estado de seleção de retângulo
    let selecting = false;
    let selStart = null; // {r,c}
    let selEnd = null;

    // Eraser mode
    let eraserMode = false;

    // Carrega imagem da planta e inicia grid
    const img = new Image();
    img.src = imageUrl + '?v=' + Date.now();
    img.onload = () => {
        imgNaturalW = img.naturalWidth;
        imgNaturalH = img.naturalHeight;
        viewer.style.backgroundImage = `url(${imageUrl})`;
        initSizesAndPosition();
        createGrid();
        loadSetores(); // preenche sectorGrid e legenda
    };

    // Ajusta tamanho base e centraliza imagem
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
        gridOverlay.style.transform = `translate(${posX}px, ${posY}px) scale(${bgW / baseBgW})`;
        gridOverlay.style.transformOrigin = 'top left';
    }

    function clampPosition() {
        const containerW = viewer.clientWidth;
        const containerH = viewer.clientHeight;
        posX = Math.min(0, Math.max(containerW - bgW, posX));
        posY = Math.min(0, Math.max(containerH - bgH, posY));
    }

    // Zoom com roda do mouse (centraliza no ponteiro)
    viewer.addEventListener('wheel', e => {
        e.preventDefault();
        const zoomSpeed = 1.12;
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
        updateGrid(); // reajusta posições/size das células
        renderAll(); // redesenha estado visível
    });

    // Pan com botão do meio (wheel click)
    viewer.addEventListener('mousedown', e => {
        // botão do meio (1) para pan
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
            // atualizar seleção se usuário estiver selecionando em cells
            const cell = getCellFromClientPoint(e.clientX, e.clientY);
            if (cell) {
                selEnd = { r: +cell.dataset.row, c: +cell.dataset.col };
                highlightSelection();
            }
        }
    });

    window.addEventListener('mouseup', e => {
        if (e.button === 1 && dragging) {
            dragging = false;
            viewer.style.cursor = 'grab';
        }

        if (selecting && e.button === 0) {
            // finalizar seleção retangular
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

                // Interações:
                // - Clique esquerdo inicia seleção retangular
                cell.addEventListener('mousedown', e => {
                    if (e.button !== 0) return;
                    e.preventDefault();
                    // modo borracha: apagar vaga inteira ao clicar em qualquer célula da vaga
                    if (eraserMode) {
                        const vagaId = cell.dataset.vagaId;
                        if (vagaId) removeVagaById(Number(vagaId));
                        return;
                    }

                    // iniciar seleção retangular
                    selecting = true;
                    selStart = { r: +cell.dataset.row, c: +cell.dataset.col };
                    selEnd = { r: selStart.r, c: selStart.c };
                    highlightSelection();
                });

                // hover enquanto seleciona é tratado pelo mousemove global (getCellFromClientPoint)
                gridOverlay.appendChild(cell);
            }
        }
        updateGrid();
    }

    // ajusta largura/altura e posição das células em função do tamanho base (baseBgW/baseBgH)
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

    // retorna o elemento .grid-cell sob as coordenadas de client (considera transform do gridOverlay)
    function getCellFromClientPoint(clientX, clientY) {
        const rect = viewer.getBoundingClientRect();
        // converte client para coordenadas locais do grid (considerando posX/posY e escala)
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

    // usa selStart/selEnd para pintar highlight temporário
    function highlightSelection() {
        removeSelectionHighlight();
        if (!selStart || !selEnd) return;
        const r1 = Math.min(selStart.r, selEnd.r);
        const r2 = Math.max(selStart.r, selEnd.r);
        const c1 = Math.min(selStart.c, selEnd.c);
        const c2 = Math.max(selStart.c, selEnd.c);
        // seleção de retângulo de no mínimo 2 células (área > 1). requisito: não permitir únicas.
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

    // finalizeSelection cria a vaga se área > 1 (mais de uma célula)
    function finalizeSelection() {
        if (!selStart || !selEnd) return;
        const r1 = Math.min(selStart.r, selEnd.r);
        const r2 = Math.max(selStart.r, selEnd.r);
        const c1 = Math.min(selStart.c, selEnd.c);
        const c2 = Math.max(selStart.c, selEnd.c);
        const height = r2 - r1 + 1;
        const width = c2 - c1 + 1;
        if (width * height <= 1) {
            // não permite criar vaga de célula única
            return;
        }

        // construir posicoes
        const posicoes = [];
        for (let r = r1; r <= r2; r++) {
            for (let c = c1; c <= c2; c++) {
                posicoes.push({ x: c, y: r });
            }
        }

        // validar que existe setor selecionado (controller exige idSetor)
        if (!selectedSetorId) {
            alert('Selecione um setor antes de criar a vaga.');
            return;
        }

        // salvar vaga temporária (mantém posicoes no objeto para render)
        vagaCounter++;
        const vagaId = Date.now() + vagaCounter; // id único temporário
        const nomeVaga = `Vaga ${vagaCounter}`;
        const vaga = {
            id: vagaId,
            nomeVaga,
            tipoVaga: currentTipo,
            idSetor: selectedSetorId,
            posicoes
        };
        vagas.push(vaga);

        // marca células com dataset.vagaId e estilo overlay
        markCellsForVaga(vaga);

        updateVagasList();
    }

    // marca células visualmente para uma vaga
    function markCellsForVaga(vaga) {
        const color = tipoColors[vaga.tipoVaga] || tipoColors.carro;
        vaga.posicoes.forEach(p => {
            const cell = gridOverlay.querySelector(`.grid-cell[data-row='${p.y}'][data-col='${p.x}']`);
            if (!cell) return;
            cell.dataset.vagaId = vaga.id;
            // aplica cor com blend (mantém setor por baixo)
            cell.style.backgroundColor = color;
        });
    }

    // remove visualmente e do array a vaga com id
    function removeVagaById(id) {
        const idx = vagas.findIndex(v => v.id === id);
        if (idx === -1) return;
        const vaga = vagas[idx];
        // limpar células
        vaga.posicoes.forEach(p => {
            const cell = gridOverlay.querySelector(`.grid-cell[data-row='${p.y}'][data-col='${p.x}']`);
            if (!cell) return;
            delete cell.dataset.vagaId;
            // re-render para provocar cor de setor de fundo ou limpar
            const setorNome = sectorGrid[p.y][p.x];
            cell.style.backgroundColor = setorNome ? setorColorWithAlpha(setorColors[setorNome]) : 'rgba(0,0,0,0.04)';
        });
        // remover do array
        vagas.splice(idx, 1);
        updateVagasList();
    }

    // limpa todas as vagas temporárias
    function clearAllVagas() {
        vagas.forEach(vaga => {
            vaga.posicoes.forEach(p => {
                const cell = gridOverlay.querySelector(`.grid-cell[data-row='${p.y}'][data-col='${p.x}']`);
                if (!cell) return;
                delete cell.dataset.vagaId;
                const setorNome = sectorGrid[p.y][p.x];
                cell.style.backgroundColor = setorNome ? setorColorWithAlpha(setorColors[setorNome]) : 'rgba(0,0,0,0.04)';
            });
        });
        vagas = [];
        vagaCounter = 0;
        updateVagasList();
    }

    // atualiza lista lateral de vagas temporárias
    function updateVagasList() {
        const container = document.getElementById('vagasList');
        container.innerHTML = '';
        vagas.forEach(v => {
            const div = document.createElement('div');
            div.className = 'd-flex justify-content-between align-items-center p-1';
            div.style.borderBottom = '1px solid rgba(0,0,0,0.05)';
            div.innerHTML = `<small>${v.nomeVaga} <span class="badge bg-light text-dark ms-2">${v.tipoVaga}</span></small>`;
            const btn = document.createElement('button');
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.textContent = 'Remover';
            btn.addEventListener('click', () => removeVagaById(v.id));
            div.appendChild(btn);
            container.appendChild(div);
        });
    }

    // ajuste visual da célula com cor do setor (com alpha)
    function setorColorWithAlpha(hexOrRgb) {
        // se já é rgba, retorna
        if (!hexOrRgb) return 'transparent';
        if (hexOrRgb.startsWith('rgba')) return hexOrRgb;
        // convert hex -> rgba(,0.25)
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

    // renderiza setor de fundo e vagas sobre as células
    function renderAll() {
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cell = gridOverlay.querySelector(`.grid-cell[data-row='${r}'][data-col='${c}']`);
                if (!cell) continue;
                // prioridade: vaga temporária > setor de fundo > padrão
                const vagaId = cell.dataset.vagaId;
                if (vagaId) {
                    // encontrar vaga para saber tipo (poderíamos derivar do vagas array)
                    const vagaObj = vagas.find(v => String(v.id) === String(vagaId));
                    cell.style.backgroundColor = vagaObj ? (tipoColors[vagaObj.tipoVaga] || tipoColors.carro) : 'rgba(0,0,0,0.06)';
                } else {
                    const setorNome = sectorGrid[r][c];
                    cell.style.backgroundColor = setorNome ? setorColorWithAlpha(setorColors[setorNome]) : 'rgba(0,0,0,0.04)';
                }
            }
        }
    }

    // === LOAD SETORES (API) e build legenda ===
    async function loadSetores() {
        try {
            const response = await fetch(`/api/setores/{{ $projeto->idProjeto }}`);
            const setores = await response.json();
            // setores pode ter múltiplos registros por setor (dependendo do backend).
            sectoresList = setores;

            // popular setorColors e, se houver coordenadas, popular sectorGrid
            setores.forEach(s => {
                if (!setorColors[s.nomeSetor]) setorColors[s.nomeSetor] = s.corSetor || '#6c757d';
                // se o backend armazenou coordenadas (setorCoordenadaX/Y) usamos para marcar o grid (pode ser só um ponto)
                if (s.setorCoordenadaY != null && s.setorCoordenadaX != null) {
                    const ry = Number(s.setorCoordenadaY);
                    const cx = Number(s.setorCoordenadaX);
                    if (ry >= 0 && ry < rows && cx >= 0 && cx < cols) {
                        sectorGrid[ry][cx] = s.nomeSetor;
                    }
                }
            });

            buildLegend(setores);
            renderAll();
        } catch (err) {
            console.error('Erro ao carregar setores:', err);
        }
    }

    // cria a legenda visual de setores (apenas leitura)
    function buildLegend(setores) {
        const legendaCard = document.getElementById('legendaSetoresCard');
        const container = document.getElementById('legendaSetores');
        container.innerHTML = '';
        const unique = [...new Map(setores.map(s => [s.nomeSetor, s])).values()];
        unique.forEach(s => {
            const el = document.createElement('div');
            el.className = 'd-flex align-items-center gap-2 p-1';
            el.style.minWidth = '100px';
            el.style.justifyContent = 'center';
            el.innerHTML = `<div style="width:18px;height:18px;border-radius:3px;background:${s.corSetor};"></div>
                            <small style="font-weight:600">${s.nomeSetor}</small>`;
            container.appendChild(el);
        });
        if (unique.length) legendaCard.style.display = 'block';
    }

    // === BUILD TIPOS TOOLBAR ===
    function buildTiposToolbar() {
        const toolbar = document.getElementById('tiposToolbar');
        toolbar.innerHTML = '';
        Object.keys(tipoColors).forEach((tipo, idx) => {
            const btn = document.createElement('button');
            btn.className = 'btn btn-sm ' + (tipo === currentTipo ? 'btn-primary' : 'btn-outline-secondary');
            btn.textContent = tipo.toUpperCase();
            btn.style.minWidth = '90px';
            btn.addEventListener('click', () => {
                currentTipo = tipo;
                Array.from(toolbar.children).forEach(b => b.classList.remove('btn-primary'));
                Array.from(toolbar.children).forEach(b => b.classList.add('btn-outline-secondary'));
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-primary');
            });
            toolbar.appendChild(btn);
        });
    }

    // inicializar toolbar
    buildTiposToolbar();

    // === CONTROLES ===
    document.getElementById('btnLimpar').addEventListener('click', () => {
        if (!confirm('Remover todas as vagas temporárias?')) return;
        clearAllVagas();
    });

    document.getElementById('btnBorracha').addEventListener('click', () => {
        eraserMode = !eraserMode;
        const btn = document.getElementById('btnBorracha');
        btn.classList.toggle('btn-secondary', eraserMode);
        btn.classList.toggle('btn-outline-secondary', !eraserMode);
    });

    // SALVAR vagas via fetch para rota vagas.store (como o controller espera: array de vagas)
    document.getElementById('btnSalvar').addEventListener('click', async () => {
        if (vagas.length === 0) return alert('⚠️ Nenhuma vaga definida!');
        // construir payload conforme requerido pelo controller (observação: controller valida 'coordenadas')
        const payload = {
            idProjeto: @json($projeto->idProjeto),
            vagas: vagas.map((v) => ({
                idSetor: v.idSetor,
                nomeVaga: v.nomeVaga,
                tipoVaga: v.tipoVaga,
                coordenadas: v.posicoes // controller espera campo "coordenadas"
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
                alert('✅ Vagas salvas com sucesso!');
                // opcional: limpar vagas temporárias após sucesso
                clearAllVagas();
            } else {
                alert('⚠️ Erro ao salvar: ' + (result.message || 'Verifique servidor.'));
            }
        } catch (err) {
            console.error(err);
            alert('❌ Falha ao comunicar com o servidor.');
        }
    });

    // rerender quando a janela redimensionar (mantém grid alinhada)
    window.addEventListener('resize', () => {
        initSizesAndPosition();
    });

    // Se quiser render manualmente (após mudanças)
    function renderGridCells() {
        updateGrid();
        renderAll();
    }

    // inicial render
    renderGridCells();

})();
</script>

<style>
/* estilos locais para as células e seleção */
.grid-cell.selecting {
    /* destaque temporário da seleção */
    mix-blend-mode: normal;
}
.grid-cell {
    transition: background-color 0.06s linear;
}
</style>
@endsection
