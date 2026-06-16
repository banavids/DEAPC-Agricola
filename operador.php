<?php
session_start();
require_once 'scripts/database.php';

// Proteção de página
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// ---------------------------------------------------------
// 1. PROCESSAR A CONCLUSÃO DE UMA TAREFA 
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['concluir_tarefa_id'])) {
    $id_tarefa = $_POST['concluir_tarefa_id'];
    
    // Atualiza a tarefa para 'Concluída'
    $query = "UPDATE tblTarefa SET TAR_estado = 'Concluída' WHERE TAR_id = :id AND TAR_responsavel_id = :user";
    db_update($query, [':id' => $id_tarefa, ':user' => $user_id]);
    
    // Regista no log a ação
    if (function_exists('registar_system_log')) {
        registar_system_log($user_id, "Tarefa Concluída", "Marcou a tarefa ID {$id_tarefa} como concluída.");
    }
    
    // Recarrega a página para limpar o POST
    header("Location: operador.php");
    exit;
}

// ---------------------------------------------------------
// 2. OBTER DADOS DO UTILIZADOR E TAREFAS
// ---------------------------------------------------------

// Obter nome do utilizador
$user_dados = db_select("SELECT USR_nome FROM tblUser WHERE USR_id = :id", [':id' => $user_id]);
$nome_completo = $user_dados ? $user_dados[0]['USR_nome'] : 'Operador';
$primeiro_nome = explode(' ', trim($nome_completo))[0]; 

// CORREÇÃO: Usar TAR_data_criacao, TAR_zona_id e TAR_prioridade
$tarefas_pendentes = db_select("
    SELECT TAR_id, TAR_descricao, TAR_data_criacao, TAR_zona_id, TAR_prioridade 
    FROM tblTarefa 
    WHERE TAR_responsavel_id = :user AND TAR_estado = 'Pendente'
    ORDER BY TAR_data_criacao DESC
", [':user' => $user_id]);

$total_pendentes = count($tarefas_pendentes);

// CORREÇÃO: Usar TAR_data_criacao
$tarefas_concluidas = db_select("
    SELECT TAR_id, TAR_descricao, TAR_data_criacao 
    FROM tblTarefa 
    WHERE TAR_responsavel_id = :user AND TAR_estado = 'Concluída'
    ORDER BY TAR_data_criacao DESC LIMIT 3
", [':user' => $user_id]);

?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Operador - FarmSmart OS</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <div class="app-layout">
        <?php require_once 'scripts/sidebar.php'; ?>
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="page-title">Painel do Operador</h1>
                </div>
                
                <div class="topbar-right">
                    <div class="notifications">
                        <i class="fa-solid fa-bell"></i>
                        <?php if ($total_pendentes > 0): ?>
                            <span class="badge"><?php echo $total_pendentes; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="user-profile">
                        <div class="avatar"><i class="fa-solid fa-user"></i></div>
                        <span class="user-name"><?php echo htmlspecialchars($nome_completo); ?></span> 
                    </div>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content dashboard-operador">
                    
                    <div class="welcome-banner">
                        <h2>Bom dia, <?php echo htmlspecialchars($primeiro_nome); ?>!</h2>
                        <p>Tens <?php echo $total_pendentes; ?> <?php echo ($total_pendentes === 1) ? 'tarefa pendente' : 'tarefas pendentes'; ?> para hoje.</p>
                        
                        <div class="production-badge">
                            <span class="badge-label">ZONA ATRIBUÍDA</span>
                            <span class="badge-value">Visão Global</span>
                        </div>
                    </div>

                    <div class="light-card">
                        <h3 class="light-card-title"><i class="fa-regular fa-square-check"></i> As Minhas Tarefas</h3>
                        
                        <div class="task-list">
                            
                            <?php if ($total_pendentes > 0): ?>
                                <?php foreach ($tarefas_pendentes as $tarefa): ?>
                                    <div class="task-item">
                                        <div class="task-icon"><i class="fa-regular fa-clock"></i></div>
                                        <div class="task-info">
                                            <h4>
                                                <?php echo htmlspecialchars($tarefa['TAR_descricao']); ?>
                                                <?php if($tarefa['TAR_prioridade'] === 'Alta'): ?>
                                                    <span style="color: #ef4444; font-size: 12px; margin-left: 8px;"><i class="fa-solid fa-triangle-exclamation"></i> Urgente</span>
                                                <?php endif; ?>
                                            </h4>
                                            <span class="task-time">
                                                Zona ID: <?php echo htmlspecialchars($tarefa['TAR_zona_id']); ?> | 
                                                Criada a: <?php echo date('d/m/Y H:i', strtotime($tarefa['TAR_data_criacao'])); ?>
                                            </span>
                                        </div>
                                        <form method="POST" style="margin: 0;">
                                            <input type="hidden" name="concluir_tarefa_id" value="<?php echo $tarefa['TAR_id']; ?>">
                                            <button type="submit" class="btn-complete">Marcar como Concluída</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="color: #64748b; padding: 15px 0;">Não tens tarefas pendentes de momento. Bom trabalho!</p>
                            <?php endif; ?>

                            <?php foreach ($tarefas_concluidas as $concluida): ?>
                                <div class="task-item task-done">
                                    <div class="task-icon text-green"><i class="fa-regular fa-circle-check"></i></div>
                                    <div class="task-info">
                                        <h4 style="text-decoration: line-through; color: #94a3b8;">
                                            <?php echo htmlspecialchars($concluida['TAR_descricao']); ?>
                                        </h4>
                                        <span class="task-time">Originalmente criada a: <?php echo date('d/m/Y', strtotime($concluida['TAR_data_criacao'])); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>

                    <div class="light-card">
                        <h3 class="light-card-title"><i class="fa-regular fa-comment"></i> Notas Recentes</h3>
                        
                        <div class="note-box">
                            <span class="note-author">Sistema Automático - <?php echo date('d/m/Y'); ?></span>
                            <p class="note-text">Não te esqueças de validar os sensores de humidade após concluíres as colheitas.</p>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script src="scripts/operador.js"></script>
</body>
</html>