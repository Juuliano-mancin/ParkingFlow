@extends('layouts.app')

@section('title', 'Associar Sensores às Vagas')

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

            <!-- DICA DE NAVEGAÇÃO -->
            <div class="position-absolute bottom-0 start-0 m-3 z-3">
                <div class="navigator-hint bg-dark bg-opacity-75 text-white px-3 py-2 rounded-3 shadow small">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-mouse text-warning"></i>
                        <span>Scroll para navegar • Clique para selecionar vagas</span>
                    </div>
                </div>
            </div>

            <div id="viewer" 
                 style="width:100%; height:100%; cursor:grab; user-select:none; background-repeat:no-repeat; background-position:center center; background-size:contain; position:relative;">
                
                <!-- Efeito de overlay durante navegação -->
                <div id="pan-overlay" class="pan-overlay" style="display:none;">
                    <div class="pan-indicator">
                        <i class="fas fa-arrows-alt"></i>
                        <span>Navegando...</span>
                    </div>
                </div>
                
                <!-- Camada de Setores (fundo) -->
                <div id="setores-layer" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
                
                <!-- Camada de Vagas (sobreposição) -->
                <div id="vagas-layer" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
            </div>
        </div>

        <!-- === PAINEL DE CONTROLE - COLUNA 3 === -->
        <div class="col-3 d-flex flex-column" style="background-color: #f8f9fa; border-left: 1px solid #dee2e6;">
            <div class="control-panel h-100 d-flex flex-column p-3 overflow-auto">
                
                <!-- Cabeçalho -->
                <div class="text-center mb-3">
                    <h5 class="text-primary mb-1">Associar Sensores</h5>
                    <p class="text-muted small mb-2">Clique nas vagas para associar sensores</p>
                </div>

                <!-- === SISTEMA DE ABAS === -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <!-- Navegação por Abas -->
                        <ul class="nav nav-pills nav-justified mb-3" id="mainTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-2" style="font-size:0.8rem;" id="projeto-tab" data-bs-toggle="pill" data-bs-target="#projeto" type="button" role="tab">
                                    <i class="fas fa-project-diagram me-1"></i>Projeto
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-2" style="font-size:0.8rem;" id="legenda-tab" data-bs-toggle="pill" data-bs-target="#legenda" type="button" role="tab">
                                    <i class="fas fa-layer-group me-1"></i>Legenda
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-2" style="font-size:0.8rem;" id="visualizacao-tab" data-bs-toggle="pill" data-bs-target="#visualizacao" type="button" role="tab">
                                    <i class="fas fa-eye me-1"></i>Visualização
                                </button>
                            </li>
                        </ul>

                        <!-- Conteúdo das Abas -->
                        <div class="tab-content">
                            
                            <!-- === ABA PROJETO === -->
                            <div class="tab-pane fade show active" id="projeto" role="tabpanel">
                                
                                <!-- Seleção de Projeto -->
                                <div class="mb-3">
                                    <label for="selectProjeto" class="form-label small text-muted mb-2">Selecionar Projeto</label>
                                    <select id="selectProjeto" class="form-select border-primary" style="font-size:0.8rem;">
                                        @foreach($projetos as $p)
                                            <option value="{{ $p->idProjeto }}"
                                                    data-caminho="{{ $p->caminhoPlantaEstacionamento }}"
                                                    {{ (isset($projeto) && $projeto->idProjeto === $p->idProjeto) ? 'selected' : '' }}>
                                                {{ $p->nomeProjeto }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Navegação Rápida -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-2">Navegação Rápida</label>
                                    <div class="d-grid gap-1">
                                        <button id="btnCenterView" class="btn btn-outline-primary btn-sm py-1">
                                            <i class="fas fa-bullseye me-1"></i>Centralizar Vista
                                        </button>
                                        <button id="btnFitToScreen" class="btn btn-outline-info btn-sm py-1">
                                            <i class="fas fa-compress-alt me-1"></i>Ajustar à Tela
                                        </button>
                                    </div>
                                </div>

                                <!-- Tela Cheia -->
                                <div class="text-center mt-3">
                                    <button id="btnFullscreen" class="btn btn-outline-success w-100 py-1" title="Tela Cheia" style="font-size:0.8rem;">
                                        <i class="fas fa-expand me-1"></i>Tela Cheia
                                    </button>
                                </div>
                            </div>

                            <!-- === ABA LEGENDA === -->
                            <div class="tab-pane fade" id="legenda" role="tabpanel">
                                
                                <!-- Legenda dos Setores -->
                                <div class="mb-3" id="legendaSetoresCard" style="display:none;">
                                    <label class="form-label small text-muted mb-2">Legenda dos Setores</label>
                                    <div id="legendaSetores" class="d-flex flex-wrap justify-content-center gap-2"></div>
                                </div>

                                <!-- Legenda dos Tipos de Vaga -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-2">Tipos de Vaga</label>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center p-2 border rounded bg-light">
                                            <div class="vaga-indicador me-2" style="width:16px; height:16px; background-color:rgba(0,123,255,0.6); border-radius:3px;"></div>
                                            <div class="vaga-label flex-grow-1" style="font-size:0.8rem;">Carro</div>
                                            <i class="fas fa-car text-primary" style="font-size:0.8rem;"></i>
                                        </div>
                                        <div class="d-flex align-items-center p-2 border rounded bg-light">
                                            <div class="vaga-indicador me-2" style="width:16px; height:16px; background-color:rgba(40,167,69,0.6); border-radius:3px;"></div>
                                            <div class="vaga-label flex-grow-1" style="font-size:0.8rem;">Moto</div>
                                            <i class="fas fa-motorcycle text-success" style="font-size:0.8rem;"></i>
                                        </div>
                                        <div class="d-flex align-items-center p-2 border rounded bg-light">
                                            <div class="vaga-indicador me-2" style="width:16px; height:16px; background-color:rgba(255,193,7,0.6); border-radius:3px;"></div>
                                            <div class="vaga-label flex-grow-1" style="font-size:0.8rem;">Idoso</div>
                                            <i class="fas fa-user-friends text-warning" style="font-size:0.8rem;"></i>
                                        </div>
                                        <div class="d-flex align-items-center p-2 border rounded bg-light">
                                            <div class="vaga-indicador me-2" style="width:16px; height:16px; background-color:rgba(108,117,125,0.6); border-radius:3px;"></div>
                                            <div class="vaga-label flex-grow-1" style="font-size:0.8rem;">Deficiente</div>
                                            <i class="fas fa-wheelchair text-secondary" style="font-size:0.8rem;"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status das Associações -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-2">Status das Associações</label>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center p-2 border rounded bg-light">
                                            <div class="vaga-indicador me-2" style="width:16px; height:16px; background-color:rgba(40,167,69,0.6); border:2px solid #28a745; border-radius:3px;"></div>
                                            <div class="vaga-label flex-grow-1" style="font-size:0.8rem;">Com Sensor</div>
                                            <i class="fas fa-check text-success" style="font-size:0.8rem;"></i>
                                        </div>
                                        <div class="d-flex align-items-center p-2 border rounded bg-light">
                                            <div class="vaga-indicador me-2" style="width:16px; height:16px; background-color:rgba(220,53,69,0.6); border:2px dashed #dc3545; border-radius:3px;"></div>
                                            <div class="vaga-label flex-grow-1" style="font-size:0.8rem;">Sem Sensor</div>
                                            <i class="fas fa-times text-danger" style="font-size:0.8rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- === ABA VISUALIZAÇÃO === -->
                            <div class="tab-pane fade" id="visualizacao" role="tabpanel">
                                
                                <!-- Controles de Zoom -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-2">Controles de Zoom</label>
                                    <div class="d-flex gap-1 align-items-center">
                                        <button id="btnZoomOutSide" class="btn btn-outline-secondary flex-grow-1 py-1" title="Zoom Out" style="font-size:0.8rem;">
                                            <i class="fas fa-search-minus"></i>
                                        </button>
                                        <button id="btnZoomResetSide" class="btn btn-outline-primary py-1" title="Reset Zoom" style="font-size:0.8rem; min-width: 50px;">
                                            100%
                                        </button>
                                        <button id="btnZoomInSide" class="btn btn-outline-secondary flex-grow-1 py-1" title="Zoom In" style="font-size:0.8rem;">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Filtros de Visualização -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-2">Filtros de Visualização</label>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="toggleSetoresCheck" checked>
                                            <label class="form-check-label small" for="toggleSetoresCheck">
                                                Mostrar Setores
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="toggleVagasCheck" checked>
                                            <label class="form-check-label small" for="toggleVagasCheck">
                                                Mostrar Vagas
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informações -->
                                <div class="text-center p-2 border rounded bg-light mt-3">
                                    <small class="text-muted d-block">Instruções</small>
                                    <small class="text-muted">Clique em uma vaga para associar um sensor</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- === VOLTAR === -->
                <div class="mt-auto pt-3">
                    <div class="text-center">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100 py-2">
                            <i class="fas fa-arrow-left me-1"></i>Voltar para Dashboard
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal para Associar Sensor -->
<div class="modal fade" id="sensorModal" tabindex="-1" aria-labelledby="sensorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title" id="sensorModalLabel" style="font-size:0.9rem;">Associar Sensor à Vaga</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 style="font-size:0.9rem;">Informações da Vaga</h6>
                        <div id="vagaInfo" class="p-2 border rounded bg-light" style="font-size:0.8rem;">
                            <!-- Informações da vaga serão preenchidas via JavaScript -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 style="font-size:0.9rem;">Selecionar Sensor</h6>
                        <div class="mb-2">
                            <label for="sensorSelect" class="form-label small">Sensores Disponíveis</label>
                            <select class="form-select" id="sensorSelect" style="font-size:0.8rem;">
                                <option value="">Selecione um sensor...</option>
                                <!-- Sensores serão preenchidos via JavaScript -->
                            </select>
                        </div>
                        <div id="sensorInfo" class="p-2 border rounded bg-light" style="display:none; font-size:0.8rem;">
                            <!-- Informações do sensor serão preenchidas via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary py-1" data-bs-dismiss="modal" style="font-size:0.8rem;">Cancelar</button>
                <button type="button" class="btn btn-primary py-1" id="btnAssociarSensor" style="font-size:0.8rem;">Associar Sensor</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Tela Cheia -->
<div id="fullscreenModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0">
            <div class="modal-header border-0 bg-dark py-2">
                <h6 class="modal-title text-white" style="font-size:0.9rem;">Visualização em Tela Cheia - Associar Sensores</h6>
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
    <symbol id="icon-sensor" viewBox="0 0 24 24">
        <path fill="currentColor" d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z"/>
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

    /* Overlay de navegação */
    .pan-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 123, 255, 0.1);
        backdrop-filter: blur(2px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }
    
    .pan-indicator {
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 12px 20px;
        border-radius: 25px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: pulse 1.5s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
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
        cursor: pointer;
    }
    
    .vaga-icon:hover { 
        transform: scale(1.2); 
        filter: brightness(1.2) drop-shadow(2px 2px 4px rgba(0,0,0,0.4));
    }
    
    .vaga-border { 
        transition: all 0.2s ease; 
        cursor: pointer;
    }
    
    .vaga-border:hover { 
        transform: scale(1.02); 
        box-shadow: 0 0 10px rgba(0,123,255,0.5);
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

    /* Estilo para vagas com sensor */
    .vaga-com-sensor {
        border-width: 3px !important;
        box-shadow: 0 0 8px rgba(40,167,69,0.5);
    }

    .vaga-sem-sensor {
        border-style: dashed !important;
    }

    /* Estados do cursor */
    .viewer-panning {
        cursor: grabbing !important;
    }
    
    .viewer-normal {
        cursor: grab;
    }
    
    .viewer-hover-vaga {
        cursor: pointer;
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
    const sensorModal = new bootstrap.Modal(document.getElementById('sensorModal'));
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
    let sensoresData = [];
    let vagasInteligentesData = [];
    let vagaSelecionada = null;
    
    // Estados dos filtros
    let showSetores = true;
    let showVagas = true;

    // === SISTEMA DE NAVEGAÇÃO COM SCROLL DO MOUSE ===
    let isPanning = false;
    let panStartX = 0;
    let panStartY = 0;
    let panStartPosX = 0;
    let panStartPosY = 0;

    // Eventos para navegação com botão do scroll
    viewer.addEventListener('mousedown', handleMouseDown);
    viewer.addEventListener('mousemove', handleMouseMove);
    viewer.addEventListener('mouseup', handleMouseUp);
    viewer.addEventListener('mouseleave', handleMouseUp);
    viewer.addEventListener('wheel', handleWheel);

    // Eventos de toque para dispositivos móveis
    viewer.addEventListener('touchstart', handleTouchStart, { passive: false });
    viewer.addEventListener('touchmove', handleTouchMove, { passive: false });
    viewer.addEventListener('touchend', handleTouchEnd);

    function handleMouseDown(e) {
        // Só inicia pan com botão do meio (scroll)
        if (e.button !== 1) return;
        
        e.preventDefault();
        startPan(e.clientX, e.clientY);
    }

    function handleMouseMove(e) {
        if (!isPanning) return;
        e.preventDefault();
        
        const rect = viewer.getBoundingClientRect();
        const currentX = e.clientX - rect.left;
        const currentY = e.clientY - rect.top;
        
        const deltaX = currentX - panStartX;
        const deltaY = currentY - panStartY;
        
        // Aplica o movimento
        posX = panStartPosX + deltaX;
        posY = panStartPosY + deltaY;
        
        clampPosition();
        applyTransform();
    }

    function handleMouseUp(e) {
        if (!isPanning) return;
        isPanning = false;
        
        viewer.classList.remove('viewer-panning');
        viewer.classList.add('viewer-normal');
        hidePanOverlay();
    }

    function handleWheel(e) {
        e.preventDefault();
        const zoomSpeed = 1.12;
        const delta = e.deltaY < 0 ? zoomSpeed : 1 / zoomSpeed;
        zoomToPoint(delta, e.clientX, e.clientY);
    }

    function startPan(clientX, clientY) {
        isPanning = true;
        const rect = viewer.getBoundingClientRect();
        
        panStartX = clientX - rect.left;
        panStartY = clientY - rect.top;
        
        panStartPosX = posX;
        panStartPosY = posY;
        
        viewer.classList.remove('viewer-normal');
        viewer.classList.add('viewer-panning');
        showPanOverlay();
    }

    function showPanOverlay() {
        const overlay = document.getElementById('pan-overlay');
        overlay.style.display = 'flex';
    }

    function hidePanOverlay() {
        const overlay = document.getElementById('pan-overlay');
        overlay.style.display = 'none';
    }

    // Suporte a toque
    function handleTouchStart(e) {
        if (e.touches.length === 1) {
            e.preventDefault();
            startPan(e.touches[0].clientX, e.touches[0].clientY);
        }
    }

    function handleTouchMove(e) {
        if (!isPanning) return;
        e.preventDefault();
        
        const rect = viewer.getBoundingClientRect();
        const currentX = e.touches[0].clientX - rect.left;
        const currentY = e.touches[0].clientY - rect.top;
        
        const deltaX = currentX - panStartX;
        const deltaY = currentY - panStartY;
        
        posX = panStartPosX + deltaX;
        posY = panStartPosY + deltaY;
        
        clampPosition();
        applyTransform();
    }

    function handleTouchEnd(e) {
        isPanning = false;
        viewer.classList.remove('viewer-panning');
        viewer.classList.add('viewer-normal');
        hidePanOverlay();
    }

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
            const temSensor = vagasInteligentesData.some(vi => vi.idVaga === vaga.idVaga);

            // Create border
            const border = document.createElement('div');
            Object.assign(border.style, {
                position: 'absolute',
                left: `${left}px`,
                top: `${top}px`,
                width: `${width}px`,
                height: `${height}px`,
                border: temSensor ? `3px solid ${vagaColor}` : `2px dashed ${vagaColor}`,
                backgroundColor: `${vagaColor}20`,
                borderRadius: '4px',
                pointerEvents: 'none',
                zIndex: '5'
            });
            if (temSensor) {
                border.style.boxShadow = '0 0 8px rgba(40,167,69,0.5)';
            }
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

            // Add sensor icon if vaga has sensor
            if (temSensor) {
                const sensorIcon = document.createElement('div');
                const sensorSize = baseIconSize * 1.2;
                Object.assign(sensorIcon.style, {
                    position: 'absolute',
                    left: `${offsetX + centerX * cellW + cellW/2 + iconSize/4}px`,
                    top: `${offsetY + centerY * cellH + cellH/2 - iconSize/4}px`,
                    width: `${sensorSize}px`,
                    height: `${sensorSize}px`,
                    color: '#28a745',
                    pointerEvents: 'none',
                    zIndex: '15',
                    filter: 'drop-shadow(1px 1px 2px rgba(0,0,0,0.3))'
                });

                sensorIcon.innerHTML = `
                    <svg width="100%" height="100%" viewBox="0 0 24 24">
                        <use xlink:href="#icon-sensor"></use>
                    </svg>
                `;

                fullscreenVagas.appendChild(sensorIcon);
            }
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
        const toggleSetoresCheck = document.getElementById('toggleSetoresCheck');
        const toggleVagasCheck = document.getElementById('toggleVagasCheck');
        
        // Sincroniza os controles
        toggleSetores.addEventListener('click', () => {
            showSetores = !showSetores;
            toggleSetores.classList.toggle('active', showSetores);
            toggleSetores.classList.toggle('btn-outline-primary', !showSetores);
            toggleSetores.classList.toggle('btn-primary', showSetores);
            toggleSetoresCheck.checked = showSetores;
            updateLayersVisibility();
        });
        
        toggleVagas.addEventListener('click', () => {
            showVagas = !showVagas;
            toggleVagas.classList.toggle('active', showVagas);
            toggleVagas.classList.toggle('btn-outline-success', !showVagas);
            toggleVagas.classList.toggle('btn-success', showVagas);
            toggleVagasCheck.checked = showVagas;
            updateLayersVisibility();
        });

        // Checkboxes também controlam os botões
        toggleSetoresCheck.addEventListener('change', () => {
            showSetores = toggleSetoresCheck.checked;
            toggleSetores.classList.toggle('active', showSetores);
            toggleSetores.classList.toggle('btn-outline-primary', !showSetores);
            toggleSetores.classList.toggle('btn-primary', showSetores);
            updateLayersVisibility();
        });

        toggleVagasCheck.addEventListener('change', () => {
            showVagas = toggleVagasCheck.checked;
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
            el.style.minWidth = '100px';
            el.style.justifyContent = 'center';
            el.style.backgroundColor = 'rgba(255,255,255,0.8)';
            el.innerHTML = `<div style="width:16px;height:16px;border-radius:4px;background:${setorColors[nome]}; border:1px solid rgba(0,0,0,0.1);"></div>
                            <small style="font-weight:600; font-size:0.8rem;">${nome}</small>`;
            legendContainer.appendChild(el);
        });
        legendCard.style.display = unique.length ? 'block' : 'none';
    }

    // load vagas and render borders + icons
    async function loadVagas(idProjeto){
        try {
            const res = await fetch(`/vagas/listar/${idProjeto}`);
            vagasData = await res.json();
            await loadVagasInteligentes();
            renderVagas();
        } catch(e){
            console.error('Erro ao carregar vagas:', e);
        }
    }

    // load sensores
    async function loadSensores(){
        try {
            const res = await fetch("{{ route('vagas.inteligentes.sensores') }}");
            sensoresData = await res.json();
            populateSensorSelect();
        } catch(e){
            console.error('Erro ao carregar sensores:', e);
        }
    }

    // load vagas inteligentes (associações existentes)
    async function loadVagasInteligentes(){
        try {
            const res = await fetch('/api/vagas-inteligentes');
            vagasInteligentesData = await res.json();
        } catch(e){
            console.error('Erro ao carregar vagas inteligentes:', e);
        }
    }

    function populateSensorSelect() {
        const select = document.getElementById('sensorSelect');
        select.innerHTML = '<option value="">Selecione um sensor...</option>';
        
        sensoresData.forEach(sensor => {
            const option = document.createElement('option');
            option.value = sensor.idSensor;
            option.textContent = `${sensor.nomeSensor} (ID: ${sensor.idSensor})`;
            select.appendChild(option);
        });
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
            const temSensor = vagasInteligentesData.some(vi => vi.idVaga === vaga.idVaga);

            // Cria a borda externa da vaga
            const border = document.createElement('div');
            border.className = `vaga-border ${temSensor ? 'vaga-com-sensor' : 'vaga-sem-sensor'}`;
            Object.assign(border.style, {
                position: 'absolute',
                left: `${left}px`,
                top: `${top}px`,
                width: `${width}px`,
                height: `${height}px`,
                border: temSensor ? `3px solid ${vagaColor}` : `2px dashed ${vagaColor}`,
                backgroundColor: `${vagaColor}20`,
                borderRadius: '4px',
                pointerEvents: 'auto',
                zIndex: '5',
                cursor: 'pointer'
            });

            // Adiciona evento de clique na borda
            border.addEventListener('click', (e) => {
                e.stopPropagation(); // Previne que o evento se propague para o viewer
                abrirModalSensor(vaga);
            });
            vagasLayer.appendChild(border);

            // Cria o ícone centralizado (200% maior)
            const icon = document.createElement('div');
            icon.className = 'vaga-icon';
            const baseIconSize = Math.min(cellW, cellH);
            const iconSize = baseIconSize * 2.0;
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
                pointerEvents: 'auto',
                zIndex: '10',
                filter: 'drop-shadow(1px 1px 2px rgba(0,0,0,0.3))',
                cursor: 'pointer'
            });

            icon.innerHTML = `
                <svg width="100%" height="100%" viewBox="0 0 24 24">
                    <use xlink:href="#${iconType}"></use>
                </svg>
            `;

            // Adiciona evento de clique no ícone
            icon.addEventListener('click', (e) => {
                e.stopPropagation(); // Previne que o evento se propague para o viewer
                abrirModalSensor(vaga);
            });
            vagasLayer.appendChild(icon);

            // Adiciona ícone de sensor se a vaga tiver sensor associado
            if (temSensor) {
                const sensorIcon = document.createElement('div');
                const sensorSize = baseIconSize * 1.2;
                Object.assign(sensorIcon.style, {
                    position: 'absolute',
                    left: `${centerX * cellW + cellW/2 + iconSize/4}px`,
                    top: `${centerY * cellH + cellH/2 - iconSize/4}px`,
                    width: `${sensorSize}px`,
                    height: `${sensorSize}px`,
                    color: '#28a745',
                    pointerEvents: 'none',
                    zIndex: '15',
                    filter: 'drop-shadow(1px 1px 2px rgba(0,0,0,0.3))'
                });

                sensorIcon.innerHTML = `
                    <svg width="100%" height="100%" viewBox="0 0 24 24">
                        <use xlink:href="#icon-sensor"></use>
                    </svg>
                `;

                vagasLayer.appendChild(sensorIcon);
            }
        });
    }

    function abrirModalSensor(vaga) {
        vagaSelecionada = vaga;
        
        // Preenche informações da vaga
        const vagaInfo = document.getElementById('vagaInfo');
        const sensorAssociado = vagasInteligentesData.find(vi => vi.idVaga === vaga.idVaga);
        const sensor = sensorAssociado ? sensoresData.find(s => s.idSensor === sensorAssociado.idSensor) : null;

        vagaInfo.innerHTML = `
            <div class="mb-2">
                <strong>Nome:</strong> ${vaga.nomeVaga}
            </div>
            <div class="mb-2">
                <strong>Tipo:</strong> ${vaga.tipoVaga}
            </div>
            <div class="mb-2">
                <strong>Setor:</strong> ${vaga.setor?.nomeSetor || 'N/A'}
            </div>
            <div class="mb-2">
                <strong>Sensor Associado:</strong> 
                ${sensor ? `${sensor.nomeSensor} (ID: ${sensor.idSensor})` : 'Nenhum'}
            </div>
            <div>
                <strong>Status:</strong> 
                <span class="badge ${sensor ? 'bg-success' : 'bg-danger'}">
                    ${sensor ? 'Com Sensor' : 'Sem Sensor'}
                </span>
            </div>
        `;

        // Preenche select de sensores
        const sensorSelect = document.getElementById('sensorSelect');
        sensorSelect.value = sensor ? sensor.idSensor : '';

        // Mostra informações do sensor selecionado
        sensorSelect.addEventListener('change', function() {
            const sensorId = this.value;
            const sensorInfo = document.getElementById('sensorInfo');
            const sensorSelecionado = sensoresData.find(s => s.idSensor == sensorId);
            
            if (sensorSelecionado) {
                sensorInfo.style.display = 'block';
                sensorInfo.innerHTML = `
                    <div class="mb-2">
                        <strong>Nome:</strong> ${sensorSelecionado.nomeSensor}
                    </div>
                    <div class="mb-2">
                        <strong>ID:</strong> ${sensorSelecionado.idSensor}
                    </div>
                    <div>
                        <strong>Status:</strong> 
                        <span class="badge ${sensorSelecionado.statusManual ? 'bg-warning' : 'bg-secondary'}">
                            ${sensorSelecionado.statusManual ? 'Manual' : 'Automático'}
                        </span>
                    </div>
                `;
            } else {
                sensorInfo.style.display = 'none';
            }
        });

        // Dispara o change event para atualizar informações
        sensorSelect.dispatchEvent(new Event('change'));

        // Abre o modal
        sensorModal.show();
    }

    // Evento para associar sensor
    document.getElementById('btnAssociarSensor').addEventListener('click', async function() {
        if (!vagaSelecionada) return;

        const sensorSelect = document.getElementById('sensorSelect');
        const sensorId = sensorSelect.value;

        if (!sensorId) {
            alert('Por favor, selecione um sensor.');
            return;
        }

        try {
            const response = await fetch("{{ route('vagaInteligente.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    idVaga: vagaSelecionada.idVaga,
                    idSensor: sensorId
                })
            });

            const result = await response.json();

            if (response.ok) {
                alert('Sensor associado com sucesso!');
                sensorModal.hide();
                
                // Recarrega as associações
                await loadVagasInteligentes();
                renderVagas();
            } else {
                alert('Erro ao associar sensor: ' + (result.message || 'Erro desconhecido'));
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao comunicar com o servidor.');
        }
    });

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
            loadSensores();
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

    // Adiciona os novos controles de navegação
    document.getElementById('btnCenterView').addEventListener('click', centerView);
    document.getElementById('btnFitToScreen').addEventListener('click', fitToScreen);

    // Sincroniza os controles de zoom duplicados
    document.getElementById('btnZoomInSide').addEventListener('click', () => {
        const rect = viewer.getBoundingClientRect();
        zoomToPoint(1.2, rect.left + rect.width / 2, rect.top + rect.height / 2);
    });
    document.getElementById('btnZoomOutSide').addEventListener('click', () => {
        const rect = viewer.getBoundingClientRect();
        zoomToPoint(1/1.2, rect.left + rect.width / 2, rect.top + rect.height / 2);
    });
    document.getElementById('btnZoomResetSide').addEventListener('click', () => {
        // Reset para 100% (escala natural)
        scaleFactor = 1;
        bgW = baseBgW * scaleFactor;
        bgH = baseBgH * scaleFactor;
        centerView();
        updateZoomIndicator();
        renderVagas();
    });

    function centerView() {
        const cw = viewer.clientWidth;
        const ch = viewer.clientHeight;
        posX = (cw - bgW) / 2;
        posY = (ch - bgH) / 2;
        clampPosition();
        applyTransform();
    }

    function fitToScreen() {
        const cw = viewer.clientWidth;
        const ch = viewer.clientHeight;
        
        const scaleX = cw / imgNaturalW;
        const scaleY = ch / imgNaturalH;
        const newScale = Math.min(scaleX, scaleY) * 0.95; // 95% para dar uma margem
        
        scaleFactor = newScale;
        bgW = baseBgW * scaleFactor;
        bgH = baseBgH * scaleFactor;
        
        centerView();
        updateZoomIndicator();
        renderVagas();
    }

    window.addEventListener('resize', ()=> { 
        initSizesAndPosition(); 
    });

})();
</script>
@endsection