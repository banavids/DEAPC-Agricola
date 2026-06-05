<?php
session_start();
require_once 'scripts/database.php';

// Proteção de página
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}

// =========================================================================
// MOTOR DE EXPORTAÇÃO (EXCEL / XLS)
// =========================================================================
if (isset($_GET['exportar'])) {
    $tipo = $_GET['exportar'];

    // --- 1. EXPORTAÇÃO DE TAREFAS ---
    if ($tipo === 'tarefas') {
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"Relatorio_Tarefas_" . date('Y-m-d') . ".xls\"");
        header("Pragma: no-cache"); header("Expires: 0");

        $query = "
            SELECT T.TAR_descricao, T.TAR_prioridade, T.TAR_estado, 
                   COALESCE(U.USR_nome, 'Sem Responsável') as Responsavel,
                   COALESCE(Z.ZON_nome, 'Sem Zona') as Zona,
                   T.TAR_data_criacao
            FROM tblTarefa T
            LEFT JOIN tblUser U ON T.TAR_responsavel_id = U.USR_id
            LEFT JOIN tblZona Z ON T.TAR_zona_id = Z.ZON_id
            ORDER BY T.TAR_estado ASC, T.TAR_data_criacao DESC
        ";
        
        echo '<meta charset="utf-8"><table border="1"><tr>';
        echo '<th style="background-color: #10b981; color: white; font-weight: bold;">Descrição da Tarefa</th>';
        echo '<th style="background-color: #10b981; color: white; font-weight: bold;">Prioridade</th>';
        echo '<th style="background-color: #10b981; color: white; font-weight: bold;">Estado</th>';
        echo '<th style="background-color: #10b981; color: white; font-weight: bold;">Responsável</th>';
        echo '<th style="background-color: #10b981; color: white; font-weight: bold;">Zona / Estufa</th>';
        echo '<th style="background-color: #10b981; color: white; font-weight: bold;">Data de Criação</th></tr>';

        try {
            $resultados = db_select($query);
            foreach ($resultados as $row) {
                $corPrio = ($row['TAR_prioridade'] == 'Alta') ? 'color: red; font-weight: bold;' : (($row['TAR_prioridade'] == 'Baixa') ? 'color: gray;' : '');
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['TAR_descricao']) . '</td>';
                echo '<td style="' . $corPrio . '">' . $row['TAR_prioridade'] . '</td>';
                echo '<td>' . $row['TAR_estado'] . '</td>';
                echo '<td>' . htmlspecialchars($row['Responsavel']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Zona']) . '</td>';
                echo '<td>' . $row['TAR_data_criacao'] . '</td>';
                echo '</tr>';
            }
        } catch (Exception $e) { echo '<tr><td colspan="6">Erro: ' . $e->getMessage() . '</td></tr>'; }
        echo '</table>'; exit;
    }

    // --- 2. EXPORTAÇÃO DE TELEMETRIA (SENSORES) ---
    elseif ($tipo === 'telemetria') {
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"Relatorio_Telemetria_" . date('Y-m-d') . ".xls\"");
        header("Pragma: no-cache"); header("Expires: 0");

        $query = "
            SELECT Z.ZON_nome, L.LEI_tipo_sensor, L.LEI_valor, L.LEI_data_hora
            FROM tblLeituras L
            LEFT JOIN tblZona Z ON L.LEI_zona_id = Z.ZON_id
            ORDER BY L.LEI_data_hora DESC
        ";
        
        echo '<meta charset="utf-8"><table border="1"><tr>';
        echo '<th style="background-color: #3b82f6; color: white; font-weight: bold;">Data e Hora</th>';
        echo '<th style="background-color: #3b82f6; color: white; font-weight: bold;">Zona / Estufa</th>';
        echo '<th style="background-color: #3b82f6; color: white; font-weight: bold;">Tipo de Sensor</th>';
        echo '<th style="background-color: #3b82f6; color: white; font-weight: bold;">Valor Registado</th></tr>';

        try {
            $resultados = db_select($query);
            foreach ($resultados as $row) {
                echo '<tr>';
                echo '<td>' . $row['LEI_data_hora'] . '</td>';
                echo '<td>' . htmlspecialchars($row['ZON_nome'] ?? 'Desconhecida') . '</td>';
                echo '<td>' . htmlspecialchars($row['LEI_tipo_sensor']) . '</td>';
                echo '<td>' . $row['LEI_valor'] . '</td>';
                echo '</tr>';
            }
        } catch (Exception $e) { echo '<tr><td colspan="4">Erro: ' . $e->getMessage() . '</td></tr>'; }
        echo '</table>'; exit;
    }

    // --- 3. EXPORTAÇÃO DE AUDITORIA (LOGS) ---
    elseif ($tipo === 'auditoria') {
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"Relatorio_Auditoria_" . date('Y-m-d') . ".xls\"");
        header("Pragma: no-cache"); header("Expires: 0");

        $query = "
            SELECT A.ALG_data_hora, A.ALG_ip_address, U.USR_nome, U.USR_email
            FROM tblAccessLog A
            LEFT JOIN tblUser U ON A.ALG_user_id = U.USR_id
            ORDER BY A.ALG_data_hora DESC
        ";
        
        echo '<meta charset="utf-8"><table border="1"><tr>';
        echo '<th style="background-color: #ef4444; color: white; font-weight: bold;">Data e Hora</th>';
        echo '<th style="background-color: #ef4444; color: white; font-weight: bold;">Utilizador</th>';
        echo '<th style="background-color: #ef4444; color: white; font-weight: bold;">Email</th>';
        echo '<th style="background-color: #ef4444; color: white; font-weight: bold;">Endereço IP</th></tr>';

        try {
            $resultados = db_select($query);
            foreach ($resultados as $row) {
                echo '<tr>';
                echo '<td>' . $row['ALG_data_hora'] . '</td>';
                echo '<td>' . htmlspecialchars($row['USR_nome'] ?? 'Utilizador Apagado') . '</td>';
                echo '<td>' . htmlspecialchars($row['USR_email'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($row['ALG_ip_address']) . '</td>';
                echo '</tr>';
            }
        } catch (Exception $e) { echo '<tr><td colspan="4">Erro: ' . $e->getMessage() . '</td></tr>'; }
        echo '</table>'; exit;
    }
}
// =========================================================================

// --- OBTER DADOS REAIS PARA OS CARTÕES HTML ---
$contagemTarefas = 0; $pendentesTarefas = 0;
$contagemLeituras = 0; $ultimaLeitura = 'Sem dados';
$contagemLogs = 0; $ultimoLog = 'Sem dados';

try { 
    $res = db_select("SELECT COUNT(*) as total, SUM(CASE WHEN TAR_estado = 'Pendente' THEN 1 ELSE 0 END) as pendentes FROM tblTarefa"); 
    $contagemTarefas = $res[0]['total'] ?? 0; 
    $pendentesTarefas = $res[0]['pendentes'] ?? 0;
} catch (Exception $e) {}

try { 
    $res = db_select("SELECT COUNT(*) as total, MAX(LEI_data_hora) as ultima FROM tblLeituras"); 
    $contagemLeituras = $res[0]['total'] ?? 0; 
    $ultimaLeitura = $res[0]['ultima'] ? date('d/m/Y H:i', strtotime($res[0]['ultima'])) : 'Sem dados';
} catch (Exception $e) {}

try { 
    $res = db_select("SELECT COUNT(*) as total, MAX(ALG_data_hora) as ultimo FROM tblAccessLog"); 
    $contagemLogs = $res[0]['total'] ?? 0; 
    $ultimoLog = $res[0]['ultimo'] ? date('d/m/Y H:i', strtotime($res[0]['ultimo'])) : 'Sem dados';
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - FarmSmart OS</title>
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
                    <button class="menu-toggle" id="menuToggle"><i class="fa-solid fa-bars"></i></button>
                    <h1 class="page-title">Relatórios e Exportações</h1>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content">
                    
                    <div class="page-header" style="margin-bottom: 30px;">
                        <h2 class="page-title">Módulo de Extração de Dados</h2>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        
                        <div class="light-card" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; border-left: 4px solid #10b981; padding: 20px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div style="background-color: #d1fae5; color: #10b981; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
                                    <i class="fa-solid fa-clipboard-list"></i>
                                </div>
                                <div>
                                    <h3 style="margin: 0 0 8px 0; font-size: 18px; color: #1e293b;">Histórico de Tarefas e Operações</h3>
                                    <p style="margin: 0; color: #64748b; font-size: 14px;">
                                        <i class="fa-solid fa-database" style="margin-right: 5px;"></i> <strong><?php echo $contagemTarefas; ?></strong> registos gravados &nbsp;|&nbsp; 
                                        <i class="fa-regular fa-clock" style="margin-right: 5px;"></i> <strong><?php echo $pendentesTarefas; ?></strong> tarefas pendentes
                                    </p>
                                </div>
                            </div>
                            <div>
                                <a href="relatorios.php?exportar=tarefas" class="btn" style="background-color: #107c41; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; display: inline-flex; align-items: center; font-weight: 600; white-space: nowrap;">
                                    <i class="fa-solid fa-file-excel" style="margin-right: 8px; font-size: 18px;"></i> Exportar Excel
                                </a>
                            </div>
                        </div>

                        <div class="light-card" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; border-left: 4px solid #3b82f6; padding: 20px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div style="background-color: #dbeafe; color: #3b82f6; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
                                    <i class="fa-solid fa-temperature-half"></i>
                                </div>
                                <div>
                                    <h3 style="margin: 0 0 8px 0; font-size: 18px; color: #1e293b;">Dados Meteorológicos e Sensores</h3>
                                    <p style="margin: 0; color: #64748b; font-size: 14px;">
                                        <i class="fa-solid fa-database" style="margin-right: 5px;"></i> <strong><?php echo $contagemLeituras; ?></strong> leituras gravadas &nbsp;|&nbsp; 
                                        <i class="fa-solid fa-tower-broadcast" style="margin-right: 5px;"></i> Última sincronização: <strong><?php echo $ultimaLeitura; ?></strong>
                                    </p>
                                </div>
                            </div>
                            <div>
                                <a href="relatorios.php?exportar=telemetria" class="btn" style="background-color: #107c41; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; display: inline-flex; align-items: center; font-weight: 600; white-space: nowrap;">
                                    <i class="fa-solid fa-file-excel" style="margin-right: 8px; font-size: 18px;"></i> Exportar Excel
                                </a>
                            </div>
                        </div>

                        <div class="light-card" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; border-left: 4px solid #ef4444; padding: 20px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div style="background-color: #fee2e2; color: #ef4444; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div>
                                    <h3 style="margin: 0 0 8px 0; font-size: 18px; color: #1e293b;">Auditoria e Log de Acessos</h3>
                                    <p style="margin: 0; color: #64748b; font-size: 14px;">
                                        <i class="fa-solid fa-database" style="margin-right: 5px;"></i> <strong><?php echo $contagemLogs; ?></strong> sessões registadas &nbsp;|&nbsp; 
                                        <i class="fa-solid fa-user-check" style="margin-right: 5px;"></i> Último login: <strong><?php echo $ultimoLog; ?></strong>
                                    </p>
                                </div>
                            </div>
                            <div>
                                <a href="relatorios.php?exportar=auditoria" class="btn" style="background-color: #107c41; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; display: inline-flex; align-items: center; font-weight: 600; white-space: nowrap;">
                                    <i class="fa-solid fa-file-excel" style="margin-right: 8px; font-size: 18px;"></i> Exportar Excel
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('active');
        });
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('open');
            this.classList.remove('active');
        });
    </script>
</body>
</html>