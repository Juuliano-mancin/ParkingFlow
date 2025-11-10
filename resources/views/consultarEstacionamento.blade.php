@extends('layouts.app')

@section('title', 'Consultar Estacionamento')

@section('content')
<div class="container-fluid p-0 vh-100">
    <div class="row g-0 h-100">

        <!-- === VIEWPORT PRINCIPAL - COLUNA 9 === -->
        <div class="col-9 position-relative" style="background-color:#f0f0f0; overflow:hidden;">
            
            <!-- Indicador de Zoom Aprimorado -->
            <div class="zoom-indicator" style="position:absolute; top:15px; right:15px; z-index:1001; background:rgba(0,0,0,0.85); color:white; padding:8px 16px; border-radius:25px; font-size:13px; font-weight:600; backdrop-filter:blur(15px); border:1px solid rgba(255,255,255,0.1); box-shadow:0 4px 12px rgba(0,0,0,0.3);">
                <i class="fas fa-search me-2"></i><span id="zoomLevel">100%</span>
            </div>

            <!-- CONTROLES DE VISUALIZAÇÃO BÁSICOS -->
            <div class="position-absolute top-0 start-0 m-3 z-3">
                <div class="btn-group shadow-lg" style="border-radius:12px; overflow:hidden;">
                    <button id="toggleSetores" class="btn btn-primary active control-btn">
                        <i class="fas fa-layer-group me-2"></i> Setores
                    </button>
                    <button id="toggleVagas" class="btn btn-success active control-btn">
                        <i class="fas fa-car me-2"></i> Vagas
                    </button>
                    <button id="toggleSensores" class="btn btn-warning active control-btn">
                        <i class="fas fa-microchip me-2"></i> Sensores
                    </button>
                </div>
            </div>

            <div id="viewer" 
                 style="width:100%; height:100%; cursor:crosshair; user-select:none; background-repeat:no-repeat; background-position:center center; background-size:contain; position:relative;">
                
                <!-- Camada de Setores (fundo) -->
                <div id="setores-layer" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
                
                <!-- Camada de Vagas (sobreposição) -->
                <div id="vagas-layer" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
                
                <!-- Camada de Sensores (sobreposição) -->
                <div id="sensores-layer" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
            </div>
        </div>

        <!-- === PAINEL DE CONTROLE - COLUNA 3 === -->
        <div class="col-3 d-flex flex-column" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 1px solid #dee2e6;">
            <div class="control-panel h-100 d-flex flex-column p-4 overflow-auto">
                
                <!-- Cabeçalho Melhorado -->
                <div class="text-center mb-4">
                    <div class="bg-primary text-white rounded-3 p-3 shadow-sm mb-3">
                        <h4 class="mb-1 fw-bold"><i class="fas fa-parking me-2"></i>Consulta do Estacionamento</h4>
                        <p class="small mb-0 opacity-90">Visualize setores, vagas e sensores em tempo real</p>
                    </div>
                </div>

                <!-- === SISTEMA DE ABAS MELHORADO === -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-3">
                        <!-- Navegação por Abas -->
                        <ul class="nav nav-pills nav-justified mb-3" id="mainTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-2" style="font-size:0.85rem;" id="projeto-tab" data-bs-toggle="pill" data-bs-target="#projeto" type="button" role="tab">
                                    <i class="fas fa-project-diagram me-1"></i>Projeto
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-2" style="font-size:0.85rem;" id="navegacao-tab" data-bs-toggle="pill" data-bs-target="#navegacao" type="button" role="tab">
                                    <i class="fas fa-arrows-alt me-1"></i>Navegação
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-2" style="font-size:0.85rem;" id="legendas-tab" data-bs-toggle="pill" data-bs-target="#legendas" type="button" role="tab">
                                    <i class="fas fa-tags me-1"></i>Legendas
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-2" style="font-size:0.85rem;" id="info-tab" data-bs-toggle="pill" data-bs-target="#info" type="button" role="tab">
                                    <i class="fas fa-info-circle me-1"></i>Informações
                                </button>
                            </li>
                        </ul>

                        <!-- Conteúdo das Abas -->
                        <div class="tab-content">
                            
                            <!-- === ABA PROJETO === -->
                            <div class="tab-pane fade show active" id="projeto" role="tabpanel">
                                <div class="mb-3">
                                    <label for="selectProjeto" class="form-label small text-muted mb-2">
                                        <i class="fas fa-list me-1"></i>Selecionar Projeto
                                    </label>
                                    <select id="selectProjeto" class="form-select border-primary shadow-sm">
                                        @foreach($projetos as $p)
                                            <option value="{{ $p->idProjeto }}"
                                                    data-caminho="{{ $p->caminhoPlantaEstacionamento }}"
                                                    {{ (isset($projeto) && $projeto->idProjeto === $p->idProjeto) ? 'selected' : '' }}>
                                                {{ $p->nomeProjeto }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status do Projeto -->
                                <div class="p-3 border rounded bg-light mt-3">
                                    <h6 class="text-center mb-2 text-secondary small">
                                        <i class="fas fa-chart-bar me-1"></i>Resumo do Projeto
                                    </h6>
                                    <div class="row text-center small">
                                        <div class="col-6 mb-2">
                                            <div class="text-primary fw-bold" id="totalVagas">0</div>
                                            <div class="text-muted">Total Vagas</div>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <div class="text-success fw-bold" id="vagasLivres">0</div>
                                            <div class="text-muted">Vagas Livres</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-warning fw-bold" id="totalSetores">0</div>
                                            <div class="text-muted">Setores</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-info fw-bold" id="sensoresAtivos">0</div>
                                            <div class="text-muted">Sensores</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- === ABA NAVEGAÇÃO === -->
                            <div class="tab-pane fade" id="navegacao" role="tabpanel">
                                
                                <!-- Controles de Zoom -->
                                <div class="mb-4">
                                    <h6 class="text-center mb-3 text-secondary small">
                                        <i class="fas fa-search me-1"></i>Controles de Zoom
                                    </h6>
                                    <div class="d-flex gap-2 align-items-center justify-content-center">
                                        <button id="btnZoomOut" class="btn btn-outline-secondary flex-grow-1 py-2" title="Zoom Out">
                                            <i class="fas fa-search-minus"></i>
                                            <small class="d-block mt-1">Zoom Out</small>
                                        </button>
                                        <button id="btnZoomReset" class="btn btn-outline-primary py-2" title="Reset Zoom" style="min-width: 70px;">
                                            <span class="fw-bold">100%</span>
                                            <small class="d-block mt-1">Reset</small>
                                        </button>
                                        <button id="btnZoomIn" class="btn btn-outline-secondary flex-grow-1 py-2" title="Zoom In">
                                            <i class="fas fa-search-plus"></i>
                                            <small class="d-block mt-1">Zoom In</small>
                                        </button>
                                    </div>
                                </div>

                                <!-- Controles de Navegação -->
                                <div class="mb-3">
                                    <h6 class="text-center mb-3 text-secondary small">
                                        <i class="fas fa-arrows-alt me-1"></i>Navegação no Mapa
                                    </h6>
                                    <div class="d-flex flex-column align-items-center">
                                        <button id="btnUp" class="btn btn-outline-secondary mb-2 px-4 py-3" title="Mover para Cima">
                                            <i class="fas fa-arrow-up fa-lg"></i>
                                        </button>
                                        <div class="d-flex gap-2 w-100 justify-content-center align-items-center">
                                            <button id="btnLeft" class="btn btn-outline-secondary flex-grow-1 py-3" title="Mover para Esquerda">
                                                <i class="fas fa-arrow-left fa-lg"></i>
                                            </button>
                                            <button id="btnCenter" class="btn btn-outline-primary px-4 py-3 mx-2" title="Centralizar Visualização">
                                                <i class="fas fa-bullseye fa-lg"></i>
                                            </button>
                                            <button id="btnRight" class="btn btn-outline-secondary flex-grow-1 py-3" title="Mover para Direita">
                                                <i class="fas fa-arrow-right fa-lg"></i>
                                            </button>
                                        </div>
                                        <button id="btnDown" class="btn btn-outline-secondary mt-2 px-4 py-3" title="Mover para Baixo">
                                            <i class="fas fa-arrow-down fa-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Atalhos de Teclado -->
                                <div class="mt-4 p-3 border rounded bg-light">
                                    <h6 class="text-center mb-2 text-secondary small">
                                        <i class="fas fa-keyboard me-1"></i>Atalhos de Teclado
                                    </h6>
                                    <div class="small text-center text-muted">
                                        <div class="row">
                                            <div class="col-6 mb-1">
                                                <kbd class="bg-dark">Roda Mouse</kbd>
                                                <div class="text-muted">Zoom</div>
                                            </div>
                                            <div class="col-6 mb-1">
                                                <kbd class="bg-dark">Botão Meio</kbd>
                                                <div class="text-muted">Arrastar</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- === ABA LEGENDAS === -->
                            <div class="tab-pane fade" id="legendas" role="tabpanel">
                                
                                <!-- Legenda dos Setores -->
                                <div class="mb-3" id="legendaSetoresCard">
                                    <h6 class="text-center mb-2 text-secondary small">
                                        <i class="fas fa-layer-group me-1"></i>Setores
                                    </h6>
                                    <div id="legendaSetores" class="d-flex flex-column gap-1"></div>
                                </div>

                                <!-- Legenda dos Tipos de Vaga -->
                                <div class="mb-3">
                                    <h6 class="text-center mb-2 text-secondary small">
                                        <i class="fas fa-tags me-1"></i>Tipos de Vaga
                                    </h6>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center p-2 border rounded bg-white shadow-sm legenda-item">
                                            <div class="vaga-indicador me-3" style="width:20px; height:20px; background-color:rgba(0,123,255,0.6); border-radius:4px;"></div>
                                            <div class="vaga-label flex-grow-1 small">Carro</div>
                                            <i class="fas fa-car text-primary"></i>
                                        </div>
                                        <div class="d-flex align-items-center p-2 border rounded bg-white shadow-sm legenda-item">
                                            <div class="vaga-indicador me-3" style="width:20px; height:20px; background-color:rgba(40,167,69,0.6); border-radius:4px;"></div>
                                            <div class="vaga-label flex-grow-1 small">Moto</div>
                                            <i class="fas fa-motorcycle text-success"></i>
                                        </div>
                                        <div class="d-flex align-items-center p-2 border rounded bg-white shadow-sm legenda-item">
                                            <div class="vaga-indicador me-3" style="width:20px; height:20px; background-color:rgba(255,193,7,0.6); border-radius:4px;"></div>
                                            <div class="vaga-label flex-grow-1 small">Idoso</div>
                                            <i class="fas fa-user-friends text-warning"></i>
                                        </div>
                                        <div class="d-flex align-items-center p-2 border rounded bg-white shadow-sm legenda-item">
                                            <div class="vaga-indicador me-3" style="width:20px; height:20px; background-color:rgba(108,117,125,0.6); border-radius:4px;"></div>
                                            <div class="vaga-label flex-grow-1 small">Deficiente</div>
                                            <i class="fas fa-wheelchair text-secondary"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Legenda dos Sensores -->
                                <div class="mb-3">
                                    <h6 class="text-center mb-2 text-secondary small">
                                        <i class="fas fa-microchip me-1"></i>Status dos Sensores
                                    </h6>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center p-2 border rounded bg-white shadow-sm legenda-item">
                                            <div class="sensor-indicador me-3" style="width:20px; height:20px; background-color:rgba(40,167,69,0.8); border-radius:50%; border:2px solid #28a745;"></div>
                                            <div class="sensor-label flex-grow-1 small">Vaga Livre</div>
                                            <i class="fas fa-check text-success"></i>
                                        </div>
                                        <div class="d-flex align-items-center p-2 border rounded bg-white shadow-sm legenda-item">
                                            <div class="sensor-indicador me-3" style="width:20px; height:20px; background-color:rgba(220,53,69,0.8); border-radius:50%; border:2px solid #dc3545;"></div>
                                            <div class="sensor-label flex-grow-1 small">Vaga Ocupada</div>
                                            <i class="fas fa-times text-danger"></i>
                                        </div>
                                        <div class="d-flex align-items-center p-2 border rounded bg-white shadow-sm legenda-item">
                                            <div class="sensor-indicador me-3" style="width:20px; height:20px; background-color:rgba(255,193,7,0.8); border-radius:50%; border:2px dashed #ffc107;"></div>
                                            <div class="sensor-label flex-grow-1 small">Vaga sem Sensor</div>
                                            <i class="fas fa-question text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- === ABA INFORMAÇÕES === -->
                            <div class="tab-pane fade" id="info" role="tabpanel">
                                
                                <!-- Informações do Sensor Selecionado -->
                                <div class="mb-3">
                                    <h6 class="text-center mb-2 text-secondary small">
                                        <i class="fas fa-info-circle me-1"></i>Informações do Sensor
                                    </h6>
                                    <div id="sensorInfo" class="text-center p-3 border rounded bg-white shadow-sm">
                                        <div class="text-muted small">
                                            <i class="fas fa-mouse-pointer me-1"></i>
                                            Passe o mouse sobre um sensor para ver informações detalhadas
                                        </div>
                                    </div>
                                </div>

                                <!-- Estatísticas Rápidas -->
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-center mb-2 text-secondary small">
                                        <i class="fas fa-chart-pie me-1"></i>Estatísticas
                                    </h6>
                                    <div class="small">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Ocupação:</span>
                                            <span id="ocupacaoPercentual" class="fw-bold">0%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 6px;">
                                            <div id="ocupacaoBar" class="progress-bar bg-success" style="width: 0%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Disponibilidade:</span>
                                            <span id="disponibilidadeText" class="fw-bold text-success">Excelente</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tela Cheia -->
                                <div class="text-center mt-4">
                                    <button id="btnFullscreen" class="btn btn-outline-success w-100 py-2" title="Tela Cheia">
                                        <i class="fas fa-expand me-2"></i>Visualização em Tela Cheia
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- === VOLTAR === -->
                <div class="mt-auto pt-4">
                    <div class="text-center">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100 py-3 shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i>Voltar para Dashboard
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal para Tela Cheia Melhorado -->
<div id="fullscreenModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0 bg-dark">
            <div class="modal-header border-secondary py-3">
                <h5 class="modal-title text-white">
                    <i class="fas fa-expand me-2"></i>Visualização em Tela Cheia
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 position-relative">
                <div id="fullscreenViewer" 
                     style="width:100%; height:100%; background-repeat:no-repeat; background-position:center center; background-size:contain;"></div>
                <div id="fullscreenSetores" 
                     style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
                <div id="fullscreenVagas" 
                     style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
                <div id="fullscreenSensores" 
                     style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></div>
                
                <!-- Controles em Tela Cheia -->
                <div class="position-absolute bottom-0 start-50 translate-middle-x mb-4">
                    <div class="btn-group shadow">
                        <button id="fsZoomOut" class="btn btn-outline-light">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button id="fsZoomReset" class="btn btn-outline-light">
                            100%
                        </button>
                        <button id="fsZoomIn" class="btn btn-outline-light">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ícones SVG para as vagas e sensores -->
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
    /* === ESTILOS MELHORADOS === */
    .btn {
        transition: all 0.3s ease;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn:active {
        transform: translateY(0);
    }
    
    .control-btn {
        border-radius: 0 !important;
        transition: all 0.3s ease;
    }
    
    .control-btn:first-child {
        border-radius: 8px 0 0 8px !important;
    }
    
    .control-btn:last-child {
        border-radius: 0 8px 8px 0 !important;
    }
    
    .card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        transform: translateY(-1px);
    }
    
    .modal-fullscreen .modal-content {
        border-radius: 0;
    }
    
    #fullscreenViewer {
        background-color: #000;
    }
    
    /* Indicador de zoom melhorado */
    .zoom-indicator {
        transition: all 0.3s ease;
        opacity: 0.9;
    }
    
    .zoom-indicator:hover {
        opacity: 1;
        transform: scale(1.05);
    }

    /* Animações melhoradas */
    @keyframes zoomPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .zoom-pulse {
        animation: zoomPulse 0.4s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.5s ease;
    }

    /* Grid e elementos interativos */
    .grid-cell { 
        transition: background-color 0.2s ease; 
    }
    
    .vaga-icon { 
        transition: all 0.3s ease; 
    }
    
    .vaga-icon:hover { 
        transform: scale(1.15); 
        filter: drop-shadow(0 2px 8px rgba(0,0,0,0.4));
    }
    
    .vaga-border { 
        transition: all 0.3s ease; 
    }
    
    .vaga-border:hover {
        box-shadow: 0 0 0 2px rgba(0,123,255,0.3);
    }
    
    .sensor-icon {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .sensor-icon:hover { 
        transform: scale(1.4);
        filter: drop-shadow(0 0 12px rgba(255,255,255,0.9));
    }
    
    .sensor-icon:active {
        transform: scale(1.2);
        transition: transform 0.1s ease;
    }

    /* Navegação por abas melhorada */
    .nav-pills .nav-link {
        font-size: 0.8rem;
        padding: 0.6rem 0.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    }

    .nav-pills .nav-link:not(.active) {
        color: #6c757d;
        background-color: rgba(0,123,255,0.08);
    }

    .nav-pills .nav-link:not(.active):hover {
        background-color: rgba(0,123,255,0.15);
        color: #007bff;
        transform: translateY(-1px);
    }

    /* Conteúdo das abas */
    .tab-content {
        min-height: 400px;
    }

    .tab-pane {
        animation: fadeIn 0.4s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateX(10px); }
        to { opacity: 1; transform: translateX(0); }
    }

    /* Botões de navegação maiores e mais visíveis */
    .nav-control-btn {
        padding: 1rem !important;
        font-size: 1.2rem !important;
        min-width: 60px;
    }

    .nav-control-btn-center {
        padding: 1rem 1.5rem !important;
        font-size: 1.2rem !important;
    }

    /* Itens de legenda melhorados */
    .legenda-item {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.08) !important;
    }
    
    .legenda-item:hover {
        transform: translateX(8px);
        background-color: rgba(0,123,255,0.05) !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* Sensor tooltip melhorado */
    .sensor-tooltip {
        position: absolute;
        background: rgba(0,0,0,0.9);
        color: white;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 12px;
        z-index: 1000;
        pointer-events: none;
        white-space: nowrap;
        transform: translateY(-100%);
        margin-top: -12px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }

    /* Animações de status */
    @keyframes statusChange {
        0% { transform: scale(1); }
        50% { transform: scale(1.3); }
        100% { transform: scale(1); }
    }

    .status-updated {
        animation: statusChange 0.6s ease;
    }

    /* Alertas personalizados */
    .status-alert {
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border: none;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }

    /* Barra de progresso customizada */
    .progress {
        border-radius: 10px;
        overflow: hidden;
        background: rgba(0,0,0,0.1);
    }

    .progress-bar {
        transition: width 0.8s ease;
        border-radius: 10px;
    }

    /* Scrollbar customizada */
    .control-panel::-webkit-scrollbar {
        width: 6px;
    }

    .control-panel::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .control-panel::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border-radius: 3px;
    }

    .control-panel::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    }

    /* Efeitos de glassmorphism */
    .glass-effect {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    /* Estilo para atalhos de teclado */
    kbd {
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Responsividade melhorada */
    @media (max-width: 1400px) {
        .col-9 { width: 70% !important; }
        .col-3 { width: 30% !important; }
        
        .nav-pills .nav-link {
            font-size: 0.75rem;
            padding: 0.5rem 0.4rem;
        }
    }

    @media (max-width: 1200px) {
        .col-9 { width: 65% !important; }
        .col-3 { width: 35% !important; }
    }

    @media (max-width: 992px) {
        .container-fluid {
            height: auto !important;
            min-height: 100vh;
        }
        
        .row.g-0 {
            flex-direction: column;
        }
        
        .col-9, .col-3 {
            width: 100% !important;
            height: 50vh;
        }
        
        .col-3 {
            height: auto;
            min-height: 50vh;
        }
        
        .nav-control-btn {
            padding: 0.75rem !important;
            font-size: 1rem !important;
        }
    }

    /* Loading states */
    .loading {
        opacity: 0.7;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
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
    const sensoresLayer = document.getElementById('sensores-layer');
    const legendCard = document.getElementById('legendaSetoresCard');
    const legendContainer = document.getElementById('legendaSetores');
    const sensorInfoEl = document.getElementById('sensorInfo');
    const fullscreenViewer = document.getElementById('fullscreenViewer');
    const fullscreenSetores = document.getElementById('fullscreenSetores');
    const fullscreenVagas = document.getElementById('fullscreenVagas');
    const fullscreenSensores = document.getElementById('fullscreenSensores');
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
    let vagasInteligentesData = [];
    let sensoresData = [];
    
    // Estados dos filtros
    let showSetores = true;
    let showVagas = true;
    let showSensores = true;

    // === FUNÇÃO updateSensorStatus ===
    async function updateSensorStatus(sensorId, newStatus) {
        try {
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (!metaToken) {
                showAlert('Erro de configuração. Recarregue a página.', 'danger');
                return;
            }
            
            const csrfToken = metaToken.getAttribute('content');
            if (!csrfToken) {
                showAlert('Token de segurança não encontrado.', 'danger');
                return;
            }

            const apiUrl = `/api/sensores/${sensorId}/toggle-status`;
            const requestOptions = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    statusManual: newStatus,
                    _method: 'PUT'
                })
            };

            const response = await fetch(apiUrl, requestOptions);

            if (response.ok) {
                const result = await response.json();
                
                const sensor = sensoresData.find(s => s.idSensor === sensorId);
                if (sensor) {
                    sensor.statusManual = newStatus;
                }
                
                renderSensores();
                updateStatistics();
                
                const statusText = newStatus ? 'ocupada' : 'livre';
                showAlert(`✅ Status da vaga atualizado para "${statusText}"!`, 'success');
                
            } else {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            
        } catch (error) {
            console.error('Erro ao atualizar sensor:', error);
            showAlert('❌ Erro ao atualizar status da vaga.', 'danger');
        }
    }

    // === FUNÇÃO PARA MOSTRAR ALERTAS ===
    function showAlert(message, type) {
        const existingAlert = document.querySelector('.status-alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show status-alert position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alert);

        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 3000);
    }

    // === INICIALIZAÇÃO COM ZOOM PARA PREENCHER A VIEWPORT ===
    function initSizesAndPosition() {
        const containerW = viewer.clientWidth;
        const containerH = viewer.clientHeight;
        
        const scaleX = containerW / imgNaturalW;
        const scaleY = containerH / imgNaturalH;
        
        const initialScale = Math.max(scaleX, scaleY) * 1.0;
        
        baseBgW = imgNaturalW;
        baseBgH = imgNaturalH;
        scaleFactor = initialScale;
        bgW = baseBgW * scaleFactor;
        bgH = baseBgH * scaleFactor;
        
        posX = (containerW - bgW) / 2;
        posY = (containerH - bgH) / 2;
        
        applyTransform();
        updateGrid();
        updateZoomIndicator();
        renderVagas();
        renderSensores();
    }

    function applyTransform() {
        viewer.style.backgroundSize = `${bgW}px ${bgH}px`;
        viewer.style.backgroundPosition = `${posX}px ${posY}px`;
        
        setoresLayer.style.transform = `translate(${posX}px, ${posY}px) scale(${scaleFactor})`;
        setoresLayer.style.transformOrigin = 'top left';
        vagasLayer.style.transform = `translate(${posX}px, ${posY}px) scale(${scaleFactor})`;
        vagasLayer.style.transformOrigin = 'top left';
        sensoresLayer.style.transform = `translate(${posX}px, ${posY}px) scale(${scaleFactor})`;
        sensoresLayer.style.transformOrigin = 'top left';
    }

    function updateZoomIndicator() {
        const percentage = Math.round(scaleFactor * 100);
        zoomLevelElement.textContent = `${percentage}%`;
        
        zoomLevelElement.classList.add('zoom-pulse');
        setTimeout(() => {
            zoomLevelElement.classList.remove('zoom-pulse');
        }, 300);
    }

    function clampPosition() {
        const cw = viewer.clientWidth;
        const ch = viewer.clientHeight;
        
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
        
        const relX = (mx - posX) / bgW;
        const relY = (my - posY) / bgH;

        scaleFactor = newScale;
        bgW = baseBgW * scaleFactor;
        bgH = baseBgH * scaleFactor;

        posX = mx - relX * bgW;
        posY = my - relY * bgH;

        clampPosition();
        applyTransform();
        updateGrid();
        updateZoomIndicator();
        renderVagas();
        renderSensores();
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
        renderSensores();
    });

    // === CONTROLES DE NAVEGAÇÃO (SETAS) ===
    function initNavigationControls() {
        const moveStep = 50;
        
        document.getElementById('btnUp').addEventListener('click', () => {
            posY += moveStep;
            clampPosition();
            applyTransform();
            renderVagas();
            renderSensores();
        });

        document.getElementById('btnDown').addEventListener('click', () => {
            posY -= moveStep;
            clampPosition();
            applyTransform();
            renderVagas();
            renderSensores();
        });

        document.getElementById('btnLeft').addEventListener('click', () => {
            posX += moveStep;
            clampPosition();
            applyTransform();
            renderVagas();
            renderSensores();
        });

        document.getElementById('btnRight').addEventListener('click', () => {
            posX -= moveStep;
            clampPosition();
            applyTransform();
            renderVagas();
            renderSensores();
        });

        document.getElementById('btnCenter').addEventListener('click', () => {
            const cw = viewer.clientWidth;
            const ch = viewer.clientHeight;
            posX = (cw - bgW) / 2;
            posY = (ch - bgH) / 2;
            clampPosition();
            applyTransform();
            renderVagas();
            renderSensores();
        });
    }

    // === SISTEMA DE ABAS ===
    function initTabSystem() {
        const tabs = document.querySelectorAll('#mainTabs .nav-link');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }

    // === TELA CHEIA ===
    document.getElementById('btnFullscreen').addEventListener('click', () => {
        fullscreenModal.show();
        setTimeout(() => {
            setupFullscreenView();
        }, 300);
    });

    function setupFullscreenView() {
        const containerW = fullscreenViewer.clientWidth;
        const containerH = fullscreenViewer.clientHeight;
        
        const scaleX = containerW / imgNaturalW;
        const scaleY = containerH / imgNaturalH;
        const scale = Math.min(scaleX, scaleY);
        
        const actualWidth = imgNaturalW * scale;
        const actualHeight = imgNaturalH * scale;
        
        const offsetX = (containerW - actualWidth) / 2;
        const offsetY = (containerH - actualHeight) / 2;
        
        fullscreenViewer.style.backgroundSize = 'contain';
        fullscreenViewer.style.backgroundPosition = 'center center';
        
        setupFullscreenLayers(scale, actualWidth, actualHeight, offsetX, offsetY);
    }

    function setupFullscreenLayers(scale, actualWidth, actualHeight, offsetX, offsetY) {
        fullscreenSetores.innerHTML = '';
        fullscreenVagas.innerHTML = '';
        fullscreenSensores.innerHTML = '';
        
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
                
                const setorNome = sectorGrid[r][c];
                if (setorNome && showSetores) {
                    cell.style.backgroundColor = setorColorWithAlpha(setorColors[setorNome]);
                }
                
                fullscreenSetores.appendChild(cell);
            }
        }
        
        if (showVagas) {
            renderFullscreenVagas(scale, actualWidth, actualHeight, offsetX, offsetY);
        }
        
        if (showSensores) {
            renderFullscreenSensores(scale, actualWidth, actualHeight, offsetX, offsetY);
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

    function renderFullscreenSensores(scale, actualWidth, actualHeight, offsetX, offsetY) {
        const cellW = actualWidth / cols;
        const cellH = actualHeight / rows;

        vagasInteligentesData.forEach(vi => {
            const vaga = vagasData.find(v => v.idVaga === vi.idVaga);
            if (!vaga) return;

            const grids = vaga.grids || [];
            if (grids.length === 0) return;

            const minX = Math.min(...grids.map(g => Number(g.posicaoVagaX)));
            const maxX = Math.max(...grids.map(g => Number(g.posicaoVagaX)));
            const minY = Math.min(...grids.map(g => Number(g.posicaoVagaY)));
            const maxY = Math.max(...grids.map(g => Number(g.posicaoVagaY)));

            const centerX = (minX + maxX) / 2;
            const centerY = (minY + maxY) / 2;

            const sensor = sensoresData.find(s => s.idSensor === vi.idSensor);
            if (!sensor) return;

            const sensorColor = sensor.statusManual ? '#dc3545' : '#28a745';
            const sensorSize = Math.min(cellW, cellH) * 1.5;

            const sensorIcon = document.createElement('div');
            sensorIcon.className = 'sensor-icon';
            Object.assign(sensorIcon.style, {
                position: 'absolute',
                left: `${offsetX + centerX * cellW + cellW/2 - sensorSize/2}px`,
                top: `${offsetY + centerY * cellH + cellH/2 - sensorSize/2}px`,
                width: `${sensorSize}px`,
                height: `${sensorSize}px`,
                color: sensorColor,
                pointerEvents: 'none',
                zIndex: '15',
                filter: 'drop-shadow(1px 1px 3px rgba(0,0,0,0.5))'
            });

            sensorIcon.innerHTML = `
                <svg width="100%" height="100%" viewBox="0 0 24 24">
                    <use xlink:href="#icon-sensor"></use>
                </svg>
            `;

            fullscreenSensores.appendChild(sensorIcon);
        });
    }

    // === CONTROLES DE ZOOM EM TELA CHEIA ===
    function initFullscreenControls() {
        document.getElementById('fsZoomIn').addEventListener('click', () => {
            const rect = fullscreenViewer.getBoundingClientRect();
            zoomToPoint(1.2, rect.left + rect.width / 2, rect.top + rect.height / 2);
        });

        document.getElementById('fsZoomOut').addEventListener('click', () => {
            const rect = fullscreenViewer.getBoundingClientRect();
            zoomToPoint(1/1.2, rect.left + rect.width / 2, rect.top + rect.height / 2);
        });

        document.getElementById('fsZoomReset').addEventListener('click', () => {
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
            renderSensores();
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
        const toggleSensores = document.getElementById('toggleSensores');
        
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
        
        toggleSensores.addEventListener('click', () => {
            showSensores = !showSensores;
            toggleSensores.classList.toggle('active', showSensores);
            toggleSensores.classList.toggle('btn-outline-warning', !showSensores);
            toggleSensores.classList.toggle('btn-warning', showSensores);
            updateLayersVisibility();
        });
    }

    function updateLayersVisibility() {
        setoresLayer.style.display = showSetores ? 'block' : 'none';
        vagasLayer.style.display = showVagas ? 'block' : 'none';
        sensoresLayer.style.display = showSensores ? 'block' : 'none';
        
        if (showSetores && showVagas && showSensores) {
            setoresLayer.style.opacity = '0.6';
            vagasLayer.style.opacity = '0.8';
            sensoresLayer.style.opacity = '1';
        } else if (showSetores && showVagas) {
            setoresLayer.style.opacity = '0.7';
            vagasLayer.style.opacity = '1';
        } else {
            setoresLayer.style.opacity = '1';
            vagasLayer.style.opacity = '1';
            sensoresLayer.style.opacity = '1';
        }
        
        renderSetores();
        renderVagas();
        renderSensores();
    }

    // init grid
    function createGrid(){
        setoresLayer.innerHTML = '';
        vagasLayer.innerHTML = '';
        sensoresLayer.innerHTML = '';
        
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
            buildLegend();
            renderSetores();
            updateStatistics();
        } catch(e){
            console.error('Erro ao carregar setores:', e);
        }
    }

    function buildLegend(){
        legendContainer.innerHTML = '';
        const unique = Object.keys(setorColors);
        unique.forEach(nome => {
            const el = document.createElement('div');
            el.className = 'd-flex align-items-center p-2 border rounded bg-white shadow-sm legenda-item';
            el.style.minWidth = '100%';
            el.style.justifyContent = 'flex-start';
            el.innerHTML = `
                <div style="width:20px;height:20px;border-radius:4px;background:${setorColors[nome]}; border:2px solid rgba(0,0,0,0.1); margin-right:12px;"></div>
                <small style="font-weight:600" class="text-dark">${nome}</small>`;
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
            updateStatistics();
        } catch(e){
            console.error('Erro ao carregar vagas:', e);
        }
    }

    // load sensores and vagas inteligentes
    async function loadSensores(){
        try {
            const sensoresRes = await fetch('/api/sensores');
            sensoresData = await sensoresRes.json();
            
            const vagasInteligentesRes = await fetch('/api/vagas-inteligentes');
            vagasInteligentesData = await vagasInteligentesRes.json();
            
            renderSensores();
            updateStatistics();
        } catch(e){
            console.error('Erro ao carregar sensores:', e);
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

            const border = document.createElement('div');
            border.className = 'vaga-border';
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
            vagasLayer.appendChild(border);

            const icon = document.createElement('div');
            icon.className = 'vaga-icon';
            const baseIconSize = Math.min(cellW, cellH);
            const iconSize = baseIconSize * 2.0;
            const iconType = tipoIcons[vaga.tipoVaga] || 'icon-car';

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

    function renderSensores() {
        if (!showSensores) return;
        
        sensoresLayer.innerHTML = '';
        const cellW = baseBgW / cols;
        const cellH = baseBgH / rows;

        vagasInteligentesData.forEach(vi => {
            const vaga = vagasData.find(v => v.idVaga === vi.idVaga);
            if (!vaga) return;

            const grids = vaga.grids || [];
            if (grids.length === 0) return;

            const minX = Math.min(...grids.map(g => Number(g.posicaoVagaX)));
            const maxX = Math.max(...grids.map(g => Number(g.posicaoVagaX)));
            const minY = Math.min(...grids.map(g => Number(g.posicaoVagaY)));
            const maxY = Math.max(...grids.map(g => Number(g.posicaoVagaY)));

            const centerX = (minX + maxX) / 2;
            const centerY = (minY + maxY) / 2;

            const sensor = sensoresData.find(s => s.idSensor === vi.idSensor);
            if (!sensor) return;

            const sensorColor = sensor.statusManual ? '#dc3545' : '#28a745';
            const sensorSize = Math.min(cellW, cellH) * 1.5;

            const sensorIcon = document.createElement('div');
            sensorIcon.className = 'sensor-icon';
            sensorIcon.title = `Clique para alterar status: ${sensor.statusManual ? 'Ocupada' : 'Livre'}`;
            
            Object.assign(sensorIcon.style, {
                position: 'absolute',
                left: `${centerX * cellW + cellW/2 - sensorSize/2}px`,
                top: `${centerY * cellH + cellH/2 - sensorSize/2}px`,
                width: `${sensorSize}px`,
                height: `${sensorSize}px`,
                color: sensorColor,
                pointerEvents: 'auto',
                zIndex: '15',
                filter: 'drop-shadow(1px 1px 3px rgba(0,0,0,0.5))',
                cursor: 'pointer'
            });

            sensorIcon.innerHTML = `
                <svg width="100%" height="100%" viewBox="0 0 24 24">
                    <use xlink:href="#icon-sensor"></use>
                </svg>
            `;

            sensorIcon.addEventListener('mouseenter', (e) => {
                const tooltip = document.createElement('div');
                tooltip.className = 'sensor-tooltip';
                tooltip.innerHTML = `
                    <strong>${sensor.nomeSensor}</strong><br>
                    Status: ${sensor.statusManual ? 'Ocupada' : 'Livre'}<br>
                    Vaga: ${vaga.nomeVaga}<br>
                    <small>Clique para alterar</small>
                `;
                tooltip.style.left = `${e.pageX + 10}px`;
                tooltip.style.top = `${e.pageY - 10}px`;
                document.body.appendChild(tooltip);

                sensorInfoEl.innerHTML = `
                    <div class="text-start">
                        <strong>Sensor:</strong> ${sensor.nomeSensor}<br>
                        <strong>ID:</strong> ${sensor.idSensor}<br>
                        <strong>Status:</strong> 
                        <span class="badge ${sensor.statusManual ? 'bg-danger' : 'bg-success'}">
                            ${sensor.statusManual ? 'Ocupada' : 'Livre'}
                        </span><br>
                        <strong>Vaga:</strong> ${vaga.nomeVaga}<br>
                        <strong>Tipo:</strong> ${vaga.tipoVaga}<br>
                        <small class="text-info mt-2 d-block">
                            <i class="fas fa-mouse-pointer me-1"></i>
                            Clique no sensor para alterar o status
                        </small>
                    </div>
                `;
            });

            sensorIcon.addEventListener('mousemove', (e) => {
                const tooltip = document.querySelector('.sensor-tooltip');
                if (tooltip) {
                    tooltip.style.left = `${e.pageX + 10}px`;
                    tooltip.style.top = `${e.pageY - 10}px`;
                }
            });

            sensorIcon.addEventListener('mouseleave', () => {
                const tooltip = document.querySelector('.sensor-tooltip');
                if (tooltip) {
                    tooltip.remove();
                }
            });

            sensorIcon.addEventListener('click', () => {
                const newStatus = !sensor.statusManual;
                const statusText = newStatus ? 'ocupada' : 'livre';
                const vagaText = vaga.nomeVaga;
                
                if (confirm(`Deseja alterar o status da vaga "${vagaText}" para "${statusText}"?`)) {
                    updateSensorStatus(sensor.idSensor, newStatus);
                }
            });

            sensoresLayer.appendChild(sensorIcon);
        });
    }

    // === ESTATÍSTICAS EM TEMPO REAL ===
    function updateStatistics() {
        const totalVagas = vagasData.length;
        const vagasComSensor = vagasInteligentesData.length;
        const sensoresLivres = sensoresData.filter(s => !s.statusManual).length;
        const ocupacaoPercentual = totalVagas > 0 ? Math.round((totalVagas - sensoresLivres) / totalVagas * 100) : 0;
        
        document.getElementById('totalVagas').textContent = totalVagas;
        document.getElementById('vagasLivres').textContent = sensoresLivres;
        document.getElementById('totalSetores').textContent = Object.keys(setorColors).length;
        document.getElementById('sensoresAtivos').textContent = vagasComSensor;
        document.getElementById('ocupacaoPercentual').textContent = ocupacaoPercentual + '%';
        document.getElementById('ocupacaoBar').style.width = ocupacaoPercentual + '%';
        
        const ocupacaoBar = document.getElementById('ocupacaoBar');
        if (ocupacaoPercentual < 50) {
            ocupacaoBar.className = 'progress-bar bg-success';
            document.getElementById('disponibilidadeText').textContent = 'Excelente';
            document.getElementById('disponibilidadeText').className = 'fw-bold text-success';
        } else if (ocupacaoPercentual < 80) {
            ocupacaoBar.className = 'progress-bar bg-warning';
            document.getElementById('disponibilidadeText').textContent = 'Moderada';
            document.getElementById('disponibilidadeText').className = 'fw-bold text-warning';
        } else {
            ocupacaoBar.className = 'progress-bar bg-danger';
            document.getElementById('disponibilidadeText').textContent = 'Crítica';
            document.getElementById('disponibilidadeText').className = 'fw-bold text-danger';
        }
    }

    // set projeto (load image, setores, vagas, sensores)
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
            sensoresLayer.innerHTML = '';
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

    // === INICIALIZAÇÃO DAS NOVAS FUNCIONALIDADES ===
    initControls();
    initNavigationControls();
    initTabSystem();
    initFullscreenControls();
    updateLayersVisibility();

    // Atualizar estatísticas periodicamente
    setInterval(updateStatistics, 5000);

    window.addEventListener('resize', ()=> { 
        initSizesAndPosition(); 
    });

})();
</script>
@endsection