/**
 * Script para atualizar o status das vagas em tempo real
 * Este arquivo contém funções para buscar e atualizar o status das vagas na interface
 */

/**
 * Função para atualizar o status das vagas
 * Busca os dados mais recentes da API e atualiza a interface
 */
function atualizarStatusVagas() {
    // Faz uma requisição para a API que retorna o status das vagas
    fetch('/api/sensors/vagas')
        .then(response => response.json()) // Converte a resposta para JSON
        .then(data => {
            // Verifica se a requisição foi bem-sucedida
            if (data.success) {
                // Obtém a lista de vagas da resposta
                const vagas = data.data;
                
                // Atualizar cada vaga na interface
                vagas.forEach(vaga => {
                    // Busca o elemento HTML da vaga pelo ID
                    const vagaElement = document.querySelector(`[data-vaga-id="${vaga.idVaga}"]`);
                    
                    // Verifica se o elemento foi encontrado
                    if (vagaElement) {
                        // Remover classes antigas (livre/ocupada)
                        vagaElement.classList.remove('vaga-livre', 'vaga-ocupada');
                        
                        // Adicionar classe baseada no status
                        if (vaga.status === 0) {
                            // Se status = 0, a vaga está livre
                            vagaElement.classList.add('vaga-livre');
                            vagaElement.setAttribute('title', 'Vaga Livre');
                        } else {
                            // Se status = 1, a vaga está ocupada
                            vagaElement.classList.add('vaga-ocupada');
                            vagaElement.setAttribute('title', 'Vaga Ocupada');
                        }
                    }
                });
            } else {
                // Se a requisição falhou, exibe o erro no console
                console.error('Erro ao buscar status das vagas:', data.message);
            }
        })
        .catch(error => {
            // Se houve erro na requisição, exibe no console
            console.error('Erro na requisição:', error);
        });
}

// Quando o documento HTML estiver completamente carregado
document.addEventListener('DOMContentLoaded', function() {
    // Primeira atualização imediata
    atualizarStatusVagas();
    
    // Configurar atualização periódica a cada 5 segundos
    // setInterval executa a função repetidamente no intervalo especificado
    setInterval(atualizarStatusVagas, 5000);
});