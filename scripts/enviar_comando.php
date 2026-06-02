<?php
session_start(); // Inicia a sessão logo a abrir!
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('phpMQTT.php');
require_once('database.php');

$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['comando'])) {
    $comando = $dados['comando'];
    
    $server = '100.125.153.75';
    $port = 1883;
    $username = '';
    $password = '';
    $client_id = 'FarmOS_Web_' . uniqid();

    // A CORREÇÃO ESTÁ AQUI: Removemos o "Bluerhinos\"
    $mqtt = new phpMQTT($server, $port, $client_id);

    if ($mqtt->connect(true, NULL, $username, $password)) {
        $mqtt->publish('farmsmart/estufa1/comandos', $comando, 0, false);
        $mqtt->close();
        
        // 2. REGISTAR O LOG NA TUA TABELA tblSystemLog
        $idUtilizador = $_SESSION['user_id'] ?? null;
        $acao = "Comando Atuador: " . $comando;
        $detalhes = "Enviou ordem via MQTT para o Raspberry Pi.";
        
        registar_system_log($idUtilizador, $acao, $detalhes);
        
        echo json_encode(['sucesso' => true, 'mensagem' => 'Comando enviado: ' . $comando]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'O PHP não conseguiu ligar ao Broker MQTT.']);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum comando recebido.']);
}
?>