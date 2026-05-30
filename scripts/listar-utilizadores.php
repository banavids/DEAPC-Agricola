<?php
session_start();
header('Content-Type: application/json');
$db_path = '../bd/FarmOS.db';

try {
    $db = new SQLite3($db_path);
    
    $query = 'SELECT u.USR_id, u.USR_nome, u.USR_email, u.USR_estado, g.USG_nome 
              FROM tblUser u 
              JOIN tblUserGroup g ON u.USR_group_id = g.USG_id 
              ORDER BY u.USR_nome ASC';
              
    $result = $db->query($query);
    $users = [];
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $users[] = $row;
    }
    
    echo json_encode($users);
    $db->close();
} catch (Exception $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}
?>