<?php
session_start();
// Desativar erros HTML para não quebrar o JSON
error_reporting(0);

// Recebe os dados enviados via Javascript (JSON)
$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['acao'])) {
    try {
        $db = new SQLite3(__DIR__ . '/../bd/FarmOS.db');
        
        // 1. CONCLUIR TAREFA
        if ($dados['acao'] === 'concluir' && isset($dados['id'])) {
            $stmt = $db->prepare("UPDATE tblTarefa SET TAR_estado = 'Concluída' WHERE TAR_id = :id");
            $stmt->bindValue(':id', $dados['id'], SQLITE3_INTEGER);
            $stmt->execute();
            
            echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa concluída!']);
        }
        
        // 2. CRIAR NOVA TAREFA
        elseif ($dados['acao'] === 'criar') {
            $stmt = $db->prepare("
                INSERT INTO tblTarefa (TAR_zona_id, TAR_responsavel_id, TAR_descricao, TAR_prioridade, TAR_estado) 
                VALUES (:zona, :resp, :desc, :prio, 'Pendente')
            ");
            $stmt->bindValue(':zona', $dados['zona_id'], SQLITE3_INTEGER);
            $stmt->bindValue(':resp', $dados['responsavel_id'], SQLITE3_INTEGER);
            $stmt->bindValue(':desc', $dados['descricao'], SQLITE3_TEXT);
            $stmt->bindValue(':prio', $dados['prioridade'], SQLITE3_TEXT);
            $stmt->execute();
            
            echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa criada com sucesso!']);
        }
        else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Ação desconhecida.']);
        }
        
        $db->close();
        
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro na BD: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum dado recebido.']);
}
?>