{{-- 
    View para o painel de vagas inteligentes
    Esta página exibe a planta do estacionamento com as vagas e seu status em tempo real
--}}

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                {{-- Cabeçalho do painel --}}
                <div class="card-header">
                    <h2>Painel de Vagas Inteligentes</h2>
                </div>
                <div class="card-body">
                    {{-- Filtros de projeto e setor --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="projeto">Selecione o Projeto:</label>
                                {{-- Dropdown para selecionar o projeto --}}
                                <select id="projeto" class="form-control" onchange="carregarSetores(this.value)">
                                    <option value="">Selecione um projeto</option>
                                    {{-- Loop pelos projetos disponíveis --}}
                                    @foreach($projetos as $projeto)
                                        <option value="{{ $projeto->idProjeto }}">{{ $projeto->nomeProjeto }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="setor">Selecione o Setor:</label>
                                {{-- Dropdown para selecionar o setor (será preenchido via JavaScript) --}}
                                <select id="setor" class="form-control" onchange="filtrarVagas(this.value)">
                                    <option value="">Selecione um setor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Container para exibir a planta e as vagas --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="planta-container">
                                {{-- Imagem da planta do estacionamento --}}
                                <img id="planta-img" src="" alt="Planta do Estacionamento" class="img-fluid" style="display: none;">
                                
                                {{-- Container para as vagas (serão posicionadas sobre a planta) --}}
                                <div id="vagas-container" class="vagas-overlay">
                                    {{-- As vagas serão carregadas dinamicamente aqui --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Legenda para os status das vagas --}}
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="legenda">
                                <span class="badge bg-success">Livre</span>
                                <span class="badge bg-danger">Ocupada</span>
                                <span class="badge bg-secondary">Sem sensor</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Importa o script para atualização em tempo real --}}
<script src="{{ asset('js/vagasInteligentes.js') }}"></script>
<script>
    /**
     * Função para carregar os setores de um projeto
     * @param {number} idProjeto - ID do projeto selecionado
     */
    function carregarSetores(idProjeto) {
        // Verifica se um projeto foi selecionado
        if (!idProjeto) return;
        
        // Faz uma requisição para a API que retorna os setores do projeto
        fetch(`/api/projetos/${idProjeto}/setores`)
            .then(response => response.json()) // Converte a resposta para JSON
            .then(data => {
                // Obtém o elemento select dos setores
                const setorSelect = document.getElementById('setor');
                
                // Limpa as opções existentes e adiciona a opção padrão
                setorSelect.innerHTML = '<option value="">Selecione um setor</option>';
                
                // Adiciona uma opção para cada setor retornado
                data.forEach(setor => {
                    const option = document.createElement('option');
                    option.value = setor.idSetor;
                    option.textContent = setor.nomeSetor;
                    setorSelect.appendChild(option);
                });
                
                // Carrega a planta do projeto
                carregarPlanta(idProjeto);
            })
            .catch(error => console.error('Erro ao carregar setores:', error));
    }
    
    /**
     * Função para carregar a planta do projeto
     * @param {number} idProjeto - ID do projeto selecionado
     */
    function carregarPlanta(idProjeto) {
        // Faz uma requisição para a API que retorna os dados do projeto
        fetch(`/api/projetos/${idProjeto}`)
            .then(response => response.json()) // Converte a resposta para JSON
            .then(data => {
                // Obtém o elemento da imagem da planta
                const plantaImg = document.getElementById('planta-img');
                
                // Define o caminho da imagem
                plantaImg.src = `/storage/${data.caminhoPlantaEstacionamento}`;
                
                // Torna a imagem visível
                plantaImg.style.display = 'block';
                
                // Carrega todas as vagas do projeto
                carregarVagas(idProjeto);
            })
            .catch(error => console.error('Erro ao carregar planta:', error));
    }
    
    /**
     * Função para carregar as vagas de um projeto
     * @param {number} idProjeto - ID do projeto selecionado
     */
    function carregarVagas(idProjeto) {
        // Faz uma requisição para a API que retorna as vagas do projeto
        fetch(`/api/projetos/${idProjeto}/vagas`)
            .then(response => response.json()) // Converte a resposta para JSON
            .then(data => {
                // Obtém o container das vagas
                const vagasContainer = document.getElementById('vagas-container');
                
                // Limpa o container
                vagasContainer.innerHTML = '';
                
                // Cria um elemento para cada vaga
                data.forEach(vaga => {
                    const vagaElement = document.createElement('div');
                    vagaElement.className = 'vaga';
                    vagaElement.dataset.vagaId = vaga.idVaga;
                    vagaElement.dataset.setorId = vaga.idSetor;
                    
                    // Posiciona a vaga conforme as coordenadas
                    vagaElement.style.left = `${vaga.posX}px`;
                    vagaElement.style.top = `${vaga.posY}px`;
                    vagaElement.style.width = `${vaga.width}px`;
                    vagaElement.style.height = `${vaga.height}px`;
                    
                    // Adiciona classe baseada no tipo de vaga
                    vagaElement.classList.add(`vaga-tipo-${vaga.tipoVaga}`);
                    
                    // Adiciona ao container
                    vagasContainer.appendChild(vagaElement);
                });
                
                // Atualiza o status das vagas
                atualizarStatusVagas();
            })
            .catch(error => console.error('Erro ao carregar vagas:', error));
    }
    
    /**
     * Função para filtrar vagas por setor
     * @param {number} idSetor - ID do setor selecionado
     */
    function filtrarVagas(idSetor) {
        // Obtém todas as vagas
        const todasVagas = document.querySelectorAll('.vaga');
        
        // Se nenhum setor foi selecionado, mostra todas as vagas
        if (!idSetor) {
            todasVagas.forEach(vaga => {
                vaga.style.display = 'block';
            });
        } else {
            // Caso contrário, mostra apenas as vagas do setor selecionado
            todasVagas.forEach(vaga => {
                if (vaga.dataset.setorId === idSetor) {
                    vaga.style.display = 'block';
                } else {
                    vaga.style.display = 'none';
                }
            });
        }
    }
</script>
@endsection

@section('styles')
<style>
    /* Container para a planta do estacionamento */
    .planta-container {
        position: relative; /* Permite posicionamento absoluto dos filhos */
        width: 100%;
        overflow: auto; /* Adiciona barras de rolagem se necessário */
    }
    
    /* Camada para as vagas sobre a planta */
    .vagas-overlay {
        position: absolute; /* Posiciona sobre a imagem da planta */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    /* Estilo base para todas as vagas */
    .vaga {
        position: absolute; /* Permite posicionamento livre */
        border: 2px solid #333;
        border-radius: 4px;
        cursor: pointer; /* Muda o cursor para indicar interatividade */
    }
    
    /* Estilo para vagas livres */
    .vaga-livre {
        background-color: rgba(40, 167, 69, 0.6); /* Verde semi-transparente */
    }
    
    /* Estilo para vagas ocupadas */
    .vaga-ocupada {
        background-color: rgba(220, 53, 69, 0.6); /* Vermelho semi-transparente */
    }
    
    /* Estilo para vagas sem sensor */
    .vaga-sem-sensor {
        background-color: rgba(108, 117, 125, 0.6); /* Cinza semi-transparente */
    }
    
    /* Estilos específicos para diferentes tipos de vagas */
    .vaga-tipo-carro {
        /* Estilo específico para vagas de carro */
    }
    
    .vaga-tipo-moto {
        /* Estilo específico para vagas de moto */
    }
    
    .vaga-tipo-deficiente {
        /* Estilo específico para vagas de deficiente */
    }
    
    .vaga-tipo-idoso {
        /* Estilo específico para vagas de idoso */
    }
    
    /* Estilo para a legenda */
    .legenda {
        display: flex;
        gap: 10px; /* Espaçamento entre os itens */
        margin-top: 10px;
    }
    
    /* Estilo para os itens da legenda */
    .legenda .badge {
        padding: 8px 12px;
    }
</style>
@endsection