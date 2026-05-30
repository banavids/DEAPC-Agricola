<?php
session_start();
$db_path = '../bd/FarmOS.db';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tarefa_id = $_POST['tarefa_id'] ?? 0;
    $novo_estado = $_POST['estado'] ?? 'Concluído'; 

    if ($tarefa_id > 0) {
        try {
            $db = new SQLite3($db_path);
            
            $stmt = $db->prepare('UPDATE tblTarefa SET TAR_estado = :estado WHERE TAR_id = :id');
            $stmt->bindValue(':estado', $novo_estado, SQLITE3_TEXT);
            $stmt->bindValue(':id', $tarefa_id, SQLITE3_INTEGER);
            $stmt->execute();
            
            // Regista o log da ação
            if(isset($_SESSION['user_id'])) {
                $logStmt = $db->prepare('INSERT INTO tblSystemLog (SLG_user_id, SLG_acao, SLG_detalhes) VALUES (:uid, "Atualizar Tarefa", "Tarefa ID " || :id || " mudou para " || :estado)');
                $logStmt->bindValue(':uid', $_SESSION['user_id'], SQLITE3_INTEGER);
                $logStmt->bindValue(':id', $tarefa_id, SQLITE3_INTEGER);
                $logStmt->bindValue(':estado', $novo_estado, SQLITE3_TEXT);
                $logStmt->execute();
            }

            $db->close();
            // Redireciona de volta para a página que enviou o pedido (pode ser o dashboard ou a página de tarefas)
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        } catch (Exception $e) {
            header("Location: ../tarefas.html?erro=servidor");
            exit;
        }
    }
}
header("Location: ../tarefas.html");
exit;
?>