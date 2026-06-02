<?php
// Função principal para ligar à base de dados (usada pelas outras funções)
function conectarBD() {
    // __DIR__ garante que ele encontra a pasta 'bd' independentemente de onde o ficheiro for chamado
    $db_path = __DIR__ . '/../bd/FarmOS.db'; 
    $db = new SQLite3($db_path);
    $db->enableExceptions(true); // Ativa os erros do SQLite para o Xdebug os apanhar se houver stress
    return $db;
}

// Função auxiliar para adivinhar automaticamente se o parâmetro é Texto ou Número
function getTipoSqlite($valor) {
    if (is_int($valor)) return SQLITE3_INTEGER;
    if (is_float($valor)) return SQLITE3_FLOAT;
    if (is_null($valor)) return SQLITE3_NULL;
    return SQLITE3_TEXT;
}

// =========================================================================
// 1. SELECT (Devolve um Array com os resultados, ou array vazio se não houver)
// =========================================================================
function db_select($query, $params = []) {
    $db = conectarBD();
    $stmt = $db->prepare($query);
    
    foreach ($params as $chave => $valor) {
        $stmt->bindValue($chave, $valor, getTipoSqlite($valor));
    }
    
    $resultado = $stmt->execute();
    $dados = [];
    
    while ($linha = $resultado->fetchArray(SQLITE3_ASSOC)) {
        $dados[] = $linha;
    }
    
    $db->close();
    return $dados;
}

// =========================================================================
// 2. INSERT (Devolve o número do ID do novo registo inserido)
// =========================================================================
function db_insert($query, $params = []) {
    $db = conectarBD();
    $stmt = $db->prepare($query);
    
    foreach ($params as $chave => $valor) {
        $stmt->bindValue($chave, $valor, getTipoSqlite($valor));
    }
    
    $stmt->execute();
    $novoId = $db->lastInsertRowID();
    $db->close();
    
    return $novoId;
}

// =========================================================================
// 3. UPDATE / DELETE (Devolve True se alterou alguma linha, False se não)
// =========================================================================
function db_update($query, $params = []) {
    $db = conectarBD();
    $stmt = $db->prepare($query);
    
    foreach ($params as $chave => $valor) {
        $stmt->bindValue($chave, $valor, getTipoSqlite($valor));
    }
    
    $stmt->execute();
    $linhasAfetadas = $db->changes(); // Conta quantas linhas foram modificadas
    $db->close();
    
    return $linhasAfetadas > 0;
}

// =========================================================================
// GRAVAR LOGS DE SISTEMA (Cliques em botões, edições, etc.)
// =========================================================================
function registar_system_log($usr_id, $acao, $detalhes = '') {
    $query = "INSERT INTO tblSystemLog (SLG_user_id, SLG_acao, SLG_detalhes) VALUES (:id, :acao, :detalhes)";
    $parametros = [
        ':id' => $usr_id,
        ':acao' => $acao,
        ':detalhes' => $detalhes
    ];
    try { db_insert($query, $parametros); } catch (Exception $e) { error_log("Erro Log: " . $e->getMessage()); }
}

// =========================================================================
// GRAVAR LOGS DE ACESSO (Logins no sistema)
// =========================================================================
function registar_access_log($usr_id) {
    // Pega no IP real de quem está a aceder ao site
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido';
    
    $query = "INSERT INTO tblAccessLog (ALG_user_id, ALG_ip_address) VALUES (:id, :ip)";
    $parametros = [
        ':id' => $usr_id,
        ':ip' => $ip
    ];
    try { db_insert($query, $parametros); } catch (Exception $e) { error_log("Erro Acesso: " . $e->getMessage()); }
}

?>

