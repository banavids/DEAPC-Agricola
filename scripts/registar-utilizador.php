<?php
session_start();
$db_path = '../bd/FarmOS.db';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $grupo = $_POST['group_id'] ?? 3; // Por defeito: 3 (Operador)

    if (!empty($nome) && !empty($email) && !empty($password)) {
        try {
            $db = new SQLite3($db_path);
            
            $checkStmt = $db->prepare('SELECT USR_id FROM tblUser WHERE USR_email = :email');
            $checkStmt->bindValue(':email', $email, SQLITE3_TEXT);
            $checkResult = $checkStmt->execute();
            
            if ($checkResult->fetchArray()) {
                header("Location: ../utilizadores.html?erro=email_existe");
                exit;
            }
            
            $stmt = $db->prepare('INSERT INTO tblUser (USR_group_id, USR_nome, USR_email, USR_password) VALUES (:grupo, :nome, :email, :password)');
            $stmt->bindValue(':grupo', $grupo, SQLITE3_INTEGER);
            $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':password', $password, SQLITE3_TEXT); 
            $stmt->execute();
            
            if(isset($_SESSION['user_id'])) {
                $logStmt = $db->prepare('INSERT INTO tblSystemLog (SLG_user_id, SLG_acao, SLG_detalhes) VALUES (:uid, "Criar Utilizador", "Email criado: " || :email)');
                $logStmt->bindValue(':uid', $_SESSION['user_id'], SQLITE3_INTEGER);
                $logStmt->bindValue(':email', $email, SQLITE3_TEXT);
                $logStmt->execute();
            }

            $db->close();
            header("Location: ../utilizadores.html?sucesso=1");
            exit;
        } catch (Exception $e) {
            header("Location: ../utilizadores.html?erro=erro_servidor");
            exit;
        }
    }
}

header("Location: ../utilizadores.html");
exit;
?>