<?php
session_start();
header('Content-Type: application/json');
$db_path = '../bd/FarmOS.db';

try {
    $db = new SQLite3($db_path);
    
    $userId = $_SESSION['user_id'] ?? 0;
    $userGroup = $_SESSION['user_group'] ?? 0;
    
    // Filtra pelo nível de acesso
    if ($userGroup == 3) {
        // Grupo 3 = Operador (vê apenas as suas)
        $query = 'SELECT t.TAR_id, t.TAR_descricao, t.TAR_prioridade, t.TAR_estado, z.ZON_nome, u.USR_nome 
                  FROM tblTarefa t 
                  LEFT JOIN tblZona z ON t.TAR_zona_id = z.ZON_id 
                  JOIN tblUser u ON t.TAR_responsavel_id = u.USR_id 
                  WHERE t.TAR_responsavel_id = :uid 
                  ORDER BY t.TAR_estado DESC, t.TAR_id DESC';
        $stmt = $db->prepare($query);
        $stmt->bindValue(':uid', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();
    } else {
        // Gestor ou Admin (veem todas)
        $query = 'SELECT t.TAR_id, t.TAR_descricao, t.TAR_prioridade, t.TAR_estado, z.ZON_nome, u.USR_nome 
                  FROM tblTarefa t 
                  LEFT JOIN tblZona z ON t.TAR_zona_id = z.ZON_id 
                  JOIN tblUser u ON t.TAR_responsavel_id = u.USR_id 
                  ORDER BY t.TAR_estado DESC, t.TAR_id DESC';
        $result = $db->query($query);
    }
    
    $tarefas = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $tarefas[] = $row;
    }
    
    echo json_encode($tarefas);
    $db->close();
} catch (Exception $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}
?>