<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('phpMQTT.php');
require_once('database.php');

header('Content-Type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['comando'])) {
    $comando = $dados['comando'];
    
    $tipo = '';
    $estado_bd = '';

    switch ($comando) {
        case 'LIGAR_REGA':
            $tipo = 'rega';
            $estado_bd = 'ligado';
            break;
        case 'DESLIGAR_REGA':
            $tipo = 'rega';
            $estado_bd = 'desligado';
            break;
        case 'ABRIR':
            $tipo = 'porta';
            $estado_bd = 'ligado'; 
            break;
        case 'FECHAR':
            $tipo = 'porta';
            $estado_bd = 'desligado'; 
            break;
        default:
            echo json_encode(['sucesso' => false, 'mensagem' => 'Comando não reconhecido.']);
            exit;
    }


    try {
        $query = "UPDATE tblAtuador SET ATU_estado = :estado WHERE ATU_tipo = :tipo";
        $parametros = [
            ':estado' => $estado_bd,
            ':tipo' => $tipo
        ];
        db_update($query, $parametros);
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Falha ao atualizar a BD: ' . $e->getMessage()]);
        exit;
    }

    $server = '100.125.153.75';
    $port = 1883;
    $username = '';
    $password = '';
    $client_id = 'FarmOS_Web_' . uniqid();

    $mqtt = new phpMQTT($server, $port, $client_id);

    if ($mqtt->connect(true, NULL, $username, $password)) {
        $mqtt->publish('farmsmart/estufa1/comandos', $comando, 0, false);
        $mqtt->close();
        
        $idUtilizador = $_SESSION['user_id'] ?? null;
        $acao = "Comando Atuador: " . $comando;
        $detalhes = "Enviou ordem via MQTT e alterou estado interno para " . strtoupper($estado_bd) . ".";
        
        if (function_exists('registar_system_log')) {
            registar_system_log($idUtilizador, $acao, $detalhes);
        }
        
        echo json_encode(['sucesso' => true, 'mensagem' => 'Comando enviado e gravado com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'O PHP não conseguiu ligar ao Broker MQTT.']);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum comando recebido.']);
}
?>