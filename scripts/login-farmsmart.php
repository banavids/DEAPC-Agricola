<?php
session_start();

// O caminho para a base de dados (recua uma pasta e entra em 'bd')
$db_path = '../bd/FarmOS.db'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        try {
            $db = new SQLite3($db_path);
            
            // Prepara a query para verificar o utilizador
            $stmt = $db->prepare('SELECT USR_id, USR_nome, USR_password, USR_group_id FROM tblUser WHERE USR_email = :email AND USR_estado = "Ativo"');
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            
            $result = $stmt->execute();
            $user = $result->fetchArray(SQLITE3_ASSOC);

            // Verifica se encontrou o utilizador e se a password está correta
            if ($user && $password === $user['USR_password']) {
                
                $userId = $user['USR_id'];
                $agora = date('Y-m-d H:i:s');
                $ip = $_SERVER['REMOTE_ADDR'];

                // Requisito 8b do guião: Atualiza a data do último acesso
                $updateStmt = $db->prepare('UPDATE tblUser SET USR_ultimo_acesso = :agora WHERE USR_id = :id');
                $updateStmt->bindValue(':agora', $agora, SQLITE3_TEXT);
                $updateStmt->bindValue(':id', $userId, SQLITE3_INTEGER);
                $updateStmt->execute();

                // Requisito do guião: Regista o acesso no histórico
                $logStmt = $db->prepare('INSERT INTO tblAccessLog (ALG_user_id, ALG_data_hora, ALG_ip_address) VALUES (:user_id, :agora, :ip)');
                $logStmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
                $logStmt->bindValue(':agora', $agora, SQLITE3_TEXT);
                $logStmt->bindValue(':ip', $ip, SQLITE3_TEXT);
                $logStmt->execute();

                

                // Guarda os dados na sessão
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_group'] = $user['USR_group_id'];
                
                $db->close();
                
                // REDIRECIONA PARA A PÁGINA DE SUCESSO (Alterado para configuracoes.php)
                header("Location: ../configuracoes.php"); 
                exit;

            } else {
                $db->close();
                // REDIRECIONA PARA O LOGIN COM ERRO (Passa a variável no link para o HTML ler)
                header("Location: ../login-farmsmart.html?erro=dados_invalidos");
                exit;
            }
        } catch (Exception $e) {
            header("Location: ../login-farmsmart.html?erro=erro_servidor");
            exit;
        }
    } else {
        header("Location: ../login-farmsmart.html?erro=campos_vazios");
        exit;
    }
}
?>