<?php

namespace App\Http\Middleware;

// Importações de classes que serão usadas neste middleware
use Closure; // Classe para encadeamento de middlewares
use Illuminate\Http\Request; // Classe para manipular requisições HTTP

/**
 * Middleware para autenticar requisições vindas de dispositivos ESP32
 * Este middleware verifica se a requisição contém uma chave de API válida
 */
class ESP32Authentication
{
    /**
     * Handle an incoming request from ESP32 devices.
     * (Manipula uma requisição recebida de dispositivos ESP32)
     *
     * @param  \Illuminate\Http\Request  $request - A requisição HTTP
     * @param  \Closure  $next - O próximo middleware na cadeia
     * @return mixed - A resposta HTTP
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtém o valor do cabeçalho 'X-ESP32-API-KEY' da requisição
        // Este cabeçalho é enviado pelo ESP32 para autenticação
        $apiKey = $request->header('X-ESP32-API-KEY');
        
        // Verifica se a chave API recebida corresponde à chave configurada
        // env('ESP32_API_KEY', 'sua_chave_secreta') busca o valor no arquivo .env
        // Se não encontrar, usa 'sua_chave_secreta' como valor padrão
        if ($apiKey !== env('ESP32_API_KEY', 'sua_chave_secreta')) {
            // Se a chave não corresponder, retorna erro 401 (Não autorizado)
            return response()->json([
                'success' => false, // Indica que a operação falhou
                'message' => 'Acesso não autorizado' // Mensagem de erro
            ], 401);
        }
        
        // Se a chave for válida, permite que a requisição continue
        // $next($request) passa a requisição para o próximo middleware ou controlador
        return $next($request);
    }
}