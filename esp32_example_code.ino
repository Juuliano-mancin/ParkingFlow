/**
 * Código de exemplo para ESP32 - Sistema de Monitoramento de Vagas
 * 
 * Este código lê o status de um sensor conectado ao ESP32 e envia os dados
 * para uma API Laravel que gerencia o sistema de estacionamento.
 * 
 * Autor: [Seu Nome]
 * Data: [Data de Criação]
 */

// Inclusão das bibliotecas necessárias
#include <WiFi.h>        // Biblioteca para conexão WiFi
#include <HTTPClient.h>  // Biblioteca para fazer requisições HTTP
#include <ArduinoJson.h> // Biblioteca para manipular JSON

// Configurações de WiFi
const char* ssid = "SeuWiFi";       // Nome da sua rede WiFi
const char* password = "SuaSenha";  // Senha da sua rede WiFi

// Configurações da API
const char* serverUrl = "https://seu-site.com/api/sensors/update"; // URL da API
const char* apiKey = "sua_chave_secreta"; // Chave de API para autenticação

// Configurações do sensor
const int sensorPin = 5;  // Pino do sensor (exemplo: sensor ultrassônico ou IR)
const int sensorId = 1;   // ID do sensor no banco de dados

// Variáveis para controle de estado
int lastStatus = -1;      // Último status enviado (-1 significa não enviado ainda)
unsigned long lastSendTime = 0; // Momento do último envio
const unsigned long sendInterval = 5000; // Intervalo de envio (5 segundos)

/**
 * Função setup - executada uma vez quando o ESP32 inicia
 * Configura os pinos, inicia a comunicação serial e conecta ao WiFi
 */
void setup() {
  // Inicializa a comunicação serial para depuração
  Serial.begin(115200);
  
  // Configura o pino do sensor como entrada
  pinMode(sensorPin, INPUT);
  
  // Conectar ao WiFi
  WiFi.begin(ssid, password);
  Serial.print("Conectando ao WiFi");
  
  // Aguarda até que a conexão seja estabelecida
  while (WiFi.status() != WL_CONNECTED) {
    delay(500); // Espera meio segundo
    Serial.print("."); // Imprime um ponto para indicar progresso
  }
  
  // Exibe informações da conexão quando conectado
  Serial.println("");
  Serial.println("WiFi conectado");
  Serial.println("Endereço IP: ");
  Serial.println(WiFi.localIP());
}

/**
 * Função loop - executada repetidamente após o setup
 * Lê o sensor, processa os dados e envia para a API
 */
void loop() {
  // Verificar se está conectado ao WiFi
  if (WiFi.status() == WL_CONNECTED) {
    
    // Ler o status do sensor (0 = livre, 1 = ocupado)
    int currentStatus = digitalRead(sensorPin);
    
    // Enviar dados apenas se o status mudou ou se passou o intervalo de tempo
    unsigned long currentTime = millis();
    if (currentStatus != lastStatus || (currentTime - lastSendTime >= sendInterval)) {
      
      // Atualizar variáveis de controle
      lastStatus = currentStatus;
      lastSendTime = currentTime;
      
      // Criar objeto JSON para enviar
      StaticJsonDocument<200> jsonDoc; // Reserva espaço para o documento JSON
      jsonDoc["sensor_id"] = sensorId; // Adiciona o ID do sensor
      jsonDoc["status"] = currentStatus; // Adiciona o status atual
      
      // Converte o objeto JSON para uma string
      String jsonString;
      serializeJson(jsonDoc, jsonString);
      
      // Enviar dados para a API
      HTTPClient http; // Cria um cliente HTTP
      http.begin(serverUrl); // Inicia a conexão com o servidor
      http.addHeader("Content-Type", "application/json"); // Define o tipo de conteúdo
      http.addHeader("X-ESP32-API-KEY", apiKey); // Adiciona a chave de API para autenticação
      
      // Envia a requisição POST com os dados JSON
      int httpResponseCode = http.POST(jsonString);
      
      // Verifica a resposta do servidor
      if (httpResponseCode > 0) {
        // Se recebeu uma resposta válida
        String response = http.getString(); // Obtém a resposta como string
        Serial.println("Código HTTP: " + String(httpResponseCode)); // Exibe o código de resposta
        Serial.println("Resposta: " + response); // Exibe a resposta
      } else {
        // Se houve erro na requisição
        Serial.println("Erro no envio HTTP: " + String(httpResponseCode));
      }
      
      // Encerra a conexão HTTP
      http.end();
    }
  }
  
  // Aguarda 1 segundo antes de verificar novamente
  delay(1000);
}