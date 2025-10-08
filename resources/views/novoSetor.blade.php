@extends('layouts.app')

@section('title', 'Novo Setor')

@section('content')
<div class="container-fluid p-0" style="height:100vh;">
    <div class="row h-100">

        <!-- Coluna maior (8/12) com viewport -->
        <div class="col-8 d-flex justify-content-center align-items-center" 
             style="background-color:#f0f0f0; border-right:1px solid #ccc; overflow:hidden; position:relative;">

            <!-- Container da viewport -->
            <div id="viewer" 
                 style="width:100%; height:100%; cursor:grab; user-select:none; -webkit-user-select:none; background-repeat:no-repeat; background-position:center center; position:relative;">
                
                <!-- Overlay do grid -->
                <div id="grid-overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;">
                    <!-- Células do grid serão criadas via JS -->
                </div>
            </div>
        </div>

        <!-- Coluna menor (4/12) com sidebar -->
        <div class="col-4 d-flex justify-content-center align-items-center">
            <div>
                <h4>Painel de Controle</h4>
                <p>Aqui poderão ficar botões, informações ou lista de setores.</p>
            </div>
        </div>

    </div>
</div>

<div class="mt-4 text-center">
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Voltar para Dashboard</a>
</div>

<script>
(function () {
    const imageUrl = @json(asset($caminhoPublico));

    const viewer = document.getElementById('viewer');
    const gridOverlay = document.getElementById('grid-overlay');

    // Estado da imagem
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

    const img = new Image();
    img.src = imageUrl;
    img.onload = () => {
        imgNaturalW = img.naturalWidth;
        imgNaturalH = img.naturalHeight;
        viewer.style.backgroundImage = `url(${imageUrl})`;
        initSizesAndPosition();
        createGrid(); // cria grid após carregar a imagem
    };

    function initSizesAndPosition() {
        const containerW = viewer.clientWidth;
        const containerH = viewer.clientHeight;
        const baseScale = Math.max(containerW / imgNaturalW, containerH / imgNaturalH);

        baseBgW = Math.round(imgNaturalW * baseScale);
        baseBgH = Math.round(imgNaturalH * baseScale);

        scaleFactor = 1;
        bgW = baseBgW * scaleFactor;
        bgH = baseBgH * scaleFactor;

        posX = Math.round((containerW - bgW) / 2);
        posY = Math.round((containerH - bgH) / 2);

        applyBg();
        updateGrid();
    }

    function applyBg() {
        viewer.style.backgroundSize = `${Math.round(bgW)}px ${Math.round(bgH)}px`;
        viewer.style.backgroundPosition = `${Math.round(posX)}px ${Math.round(posY)}px`;
    }

    function clampPositionForCurrentSize() {
        const containerW = viewer.clientWidth;
        const containerH = viewer.clientHeight;

        if (bgW <= containerW) {
            posX = Math.round((containerW - bgW) / 2);
        } else {
            posX = Math.min(0, Math.max(containerW - bgW, posX));
        }

        if (bgH <= containerH) {
            posY = Math.round((containerH - bgH) / 2);
        } else {
            posY = Math.min(0, Math.max(containerH - bgH, posY));
        }
    }

    // Zoom centrado no cursor
    viewer.addEventListener('wheel', function (ev) {
        ev.preventDefault();
        const zoomStep = 1.12;
        const delta = ev.deltaY < 0 ? zoomStep : 1 / zoomStep;
        let newScaleFactor = scaleFactor * delta;

        if (newScaleFactor < MIN_SCALE_FACTOR) newScaleFactor = MIN_SCALE_FACTOR;
        if (newScaleFactor > MAX_SCALE_FACTOR) newScaleFactor = MAX_SCALE_FACTOR;

        const rect = viewer.getBoundingClientRect();
        const mx = ev.clientX - rect.left;
        const my = ev.clientY - rect.top;

        const fracX = (mx - posX) / bgW;
        const fracY = (my - posY) / bgH;

        const newBgW = baseBgW * newScaleFactor;
        const newBgH = baseBgH * newScaleFactor;

        let newPosX = mx - fracX * newBgW;
        let newPosY = my - fracY * newBgH;

        bgW = newBgW;
        bgH = newBgH;
        scaleFactor = newScaleFactor;

        posX = newPosX;
        posY = newPosY;

        clampPositionForCurrentSize();
        applyBg();
        updateGrid();
    }, { passive: false });

    // Drag da viewport
    viewer.addEventListener('mousedown', function (ev) {
        ev.preventDefault();
        dragging = true;
        startClientX = ev.clientX;
        startClientY = ev.clientY;
        startPosX = posX;
        startPosY = posY;
        viewer.style.cursor = 'grabbing';
    });

    window.addEventListener('mousemove', function (ev) {
        if (!dragging) return;
        ev.preventDefault();
        const dx = ev.clientX - startClientX;
        const dy = ev.clientY - startClientY;

        posX = startPosX + dx;
        posY = startPosY + dy;

        clampPositionForCurrentSize();
        applyBg();
        updateGrid();
    });

    window.addEventListener('mouseup', function () {
        dragging = false;
        viewer.style.cursor = 'grab';
    });

    window.addEventListener('resize', function () {
        initSizesAndPosition();
    });

    viewer.addEventListener('dragstart', e => e.preventDefault());

    // --- GRID ---
    const rows = 25; // ajuste conforme necessário
    const cols = 25; // ajuste conforme necessário

    function createGrid() {
        gridOverlay.innerHTML = '';
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cell = document.createElement('div');
                cell.classList.add('grid-cell');
                cell.style.position = 'absolute';
                cell.style.border = '1px solid rgba(0, 132, 255, 0.26)';
                cell.dataset.row = r;
                cell.dataset.col = c;
                gridOverlay.appendChild(cell);
            }
        }
        updateGrid();
    }

    function updateGrid() {
        const cellW = bgW / cols;
        const cellH = bgH / rows;
        const containerRect = viewer.getBoundingClientRect();
        const offsetLeft = posX;
        const offsetTop = posY;

        const cells = gridOverlay.children;
        for (let i = 0; i < cells.length; i++) {
            const r = cells[i].dataset.row;
            const c = cells[i].dataset.col;
            cells[i].style.width = `${cellW}px`;
            cells[i].style.height = `${cellH}px`;
            cells[i].style.left = `${offsetLeft + c * cellW}px`;
            cells[i].style.top = `${offsetTop + r * cellH}px`;
        }
    }

})();
</script>
@endsection
