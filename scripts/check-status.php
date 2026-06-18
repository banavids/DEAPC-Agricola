<?php

ini_set('display_errors', 0);
error_reporting(0);

require_once('phpMQTT.php');

$status = [
    'servidor' => false,
    'mqtt' => false
];


try {
    $db_path = '../bd/FarmOS.db';
    if (file_exists($db_path)) {
        $db = new SQLite3($db_path);
        if ($db) {
            $status['servidor'] = true;
            $db->close();
        }
    }
} catch (Exception $e) {
    $status['servidor'] = false;
}


try {
    $server = '100.125.153.75';
    $port = 1883;
    $client_id = 'FarmOS_Ping_' . uniqid();
    
    $mqtt = new phpMQTT($server, $port, $client_id);
    
    // Tenta ligar com um timeout muito curto (ex: 2 segundos) para não bloquear a página
    if ($mqtt->connect(true, NULL, '', '')) {
        $status['mqtt'] = true;
        $mqtt->close();
    }
} catch (Exception $e) {
    $status['mqtt'] = false;
}

// Devolve o resultado em formato JSON para o Javascript ler
header('Content-Type: application/json');
echo json_encode($status);
?>