<?php
session_start();
header('Content-Type: application/json');
$db_path = '../bd/FarmOS.db';

// Verifica se o utilizador está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

try {
    $db = new SQLite3($db_path);
    $userId = $_SESSION['user_id'];
    $userGroup = $_SESSION['user_group'] ?? 3;
    
    // Se for Admin (grupo 1), vê o histórico de todos. Se não, vê apenas os seus próprios acessos.
    if ($userGroup == 1) {
        $query = 'SELECT l.ALG_data_hora, l.ALG_ip_address, u.USR_nome 
                  FROM tblAccessLog l 
                  JOIN tblUser u ON l.ALG_user_id = u.USR_id 
                  ORDER BY l.ALG_data_hora DESC LIMIT 50';
        $stmt = $db->prepare($query);
    } else {
        $query = 'SELECT ALG_data_hora, ALG_ip_address, "Eu" as USR_nome 
                  FROM tblAccessLog 
                  WHERE ALG_user_id = :id 
                  ORDER BY ALG_data_hora DESC LIMIT 50';
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
    }
    
    $result = $stmt->execute();
    $logs = [];
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $logs[] = $row;
    }
    
    // Devolve os dados em formato JSON
    echo json_encode($logs);
    $db->close();
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>