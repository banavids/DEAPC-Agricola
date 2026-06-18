<?php
session_start();
require_once 'scripts/database.php';

// Proteção de página
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}


if ($_SESSION['user_group'] != 1) {
    header("Location: operador.php"); 
    exit;
}

$user_id = $_SESSION['user_id'];

$user_dados = db_select("SELECT USR_nome FROM tblUser WHERE USR_id = :id", [':id' => $user_id]);
$nome_completo = $user_dados ? $user_dados[0]['USR_nome'] : 'Admin';
$primeiro_nome = explode(' ', trim($nome_completo))[0];


$users_db = db_select("SELECT COUNT(USR_id) as total FROM tblUser WHERE USR_estado = 'Ativo'");
$total_users = $users_db ? $users_db[0]['total'] : 0;

$sensores_total_db = db_select("SELECT COUNT(SNR_id) as total FROM tblSensor");
$sensores_online_db = db_select("SELECT COUNT(SNR_id) as total FROM tblSensor WHERE SNR_estado = 'Online'");
$total_sensores = $sensores_total_db ? $sensores_total_db[0]['total'] : 0;
$sensores_online = $sensores_online_db ? $sensores_online_db[0]['total'] : 0;
$sensores_offline = $total_sensores - $sensores_online;

$logs_hoje_db = db_select("SELECT COUNT(SLG_id) as total FROM tblSystemLog WHERE date(SLG_data_hora) = date('now')");
$total_logs_hoje = $logs_hoje_db ? $logs_hoje_db[0]['total'] : 0;

$ultimo_log_db = db_select("SELECT SLG_acao, SLG_data_hora FROM tblSystemLog ORDER BY SLG_data_hora DESC LIMIT 1");
$ultima_acao = $ultimo_log_db ? $ultimo_log_db[0]['SLG_acao'] : 'Nenhuma atividade';
$hora_ultima_acao = $ultimo_log_db ? date('H:i:s', strtotime($ultimo_log_db[0]['SLG_data_hora'])) : '--:--';


$temp_history = db_select("SELECT LEI_valor, LEI_data_hora FROM tblLeituras WHERE LEI_tipo_sensor = 'temperatura' ORDER BY LEI_data_hora DESC LIMIT 10");
if(!$temp_history) $temp_history = [];
$temp_history = array_reverse($temp_history); // Inverter para ficar cronológico no gráfico

$labels_js = [];
$data_temp_js = [];

foreach($temp_history as $row) {

    $hora = date('H:i', strtotime($row['LEI_data_hora']));
    $labels_js[] = "'" . $hora . "'";
    $data_temp_js[] = $row['LEI_valor'];
}

// Fallback caso a BD esteja vazia
if(empty($labels_js)) {
    $labels_js[] = "'Sem dados'";
    $data_temp_js[] = 0;
}

$labels_str = implode(',', $labels_js);
$data_temp_str = implode(',', $data_temp_js);

?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - FarmSmart OS</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <h1 class="page-title">Dashboard de Administração</h1>
                </div>
                
                <div class="topbar-right">
                    <div class="user-profile">
                        <div class="avatar"><i class="fa-solid fa-user-shield"></i></div>
                        <span class="user-name"><?php echo htmlspecialchars($nome_completo); ?></span>
                    </div>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content">
                    
                    <div class="page-header" style="margin-bottom: 20px;">
                        <h2 class="page-title">Visão Global do Sistema</h2>
                        <p style="color: var(--text-muted); font-size: 14px;">Bem-vindo de volta, <?php echo htmlspecialchars($primeiro_nome); ?>. Aqui está o estado atual da infraestrutura.</p>
                    </div>

                    <div class="kpi-grid kpi-admin">
                        <div class="card kpi-card">
                            <div class="kpi-info">
                                <span class="kpi-label">UTILIZADORES ATIVOS</span>
                                <span class="kpi-value"><?php echo $total_users; ?></span>
                            </div>
                            <div class="kpi-icon icon-blue"><i class="fa-solid fa-user-check"></i></div>
                        </div>

                        <div class="card kpi-card">
                            <div class="kpi-info">
                                <span class="kpi-label">SENSORES ONLINE</span>
                                <span class="kpi-value"><?php echo $sensores_online; ?>/<?php echo $total_sensores; ?></span>
                            </div>
                            <div class="kpi-icon icon-purple"><i class="fa-solid fa-microchip"></i></div>
                        </div>

                        <div class="card kpi-card">
                            <div class="kpi-info">
                                <span class="kpi-label">AÇÕES HOJE (LOGS)</span>
                                <span class="kpi-value"><?php echo $total_logs_hoje; ?></span>
                            </div>
                            <div class="kpi-icon icon-green"><i class="fa-solid fa-server"></i></div>
                        </div>

                        <div class="card kpi-card">
                            <div class="kpi-info">
                                <span class="kpi-label">ÚLTIMA ATIVIDADE</span>
                                <span class="kpi-value" style="font-size: 14px; margin-top: 5px;"><?php echo htmlspecialchars($ultima_acao); ?></span>
                                <span class="kpi-trend text-yellow" style="margin-top: 5px;"><i class="fa-regular fa-clock"></i> <?php echo $hora_ultima_acao; ?></span>
                            </div>
                            <div class="kpi-icon icon-yellow"><i class="fa-solid fa-bolt"></i></div>
                        </div>

                        <div class="card kpi-card">
                            <div class="kpi-info">
                                <span class="kpi-label">ESTADO DO BROKER MQTT</span>
                                <span class="kpi-value text-green" style="font-size: 18px; margin-top: 5px;">Online</span>
                            </div>
                            <div class="kpi-icon icon-green"><i class="fa-solid fa-tower-broadcast"></i></div>
                        </div>
                    </div>

                    <div class="grafana-grid admin-charts">
                        
                        <div class="card area-main-chart" style="position: relative; padding: 20px;">
                            <h3 class="card-title"><i class="fa-solid fa-chart-area"></i> Flutuação de Temperatura (Últimas Leituras)</h3>
                            <div style="position: relative; height: 300px; width: 100%;">
                                <canvas id="mainLineChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="card area-side-chart" style="position: relative; padding: 20px; display: flex; flex-direction: column; align-items: center;">
                            <h3 class="card-title" style="align-self: flex-start;"><i class="fa-solid fa-circle-notch"></i> Saúde da Rede (Sensores)</h3>
                            <div style="position: relative; height: 250px; width: 250px; margin-top: 20px;">
                                <canvas id="sensorDoughnutChart"></canvas>
                            </div>
                        </div>
                        
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script src="scripts/admin.js"></script>

    <script>

        Chart.defaults.color = '#94a3b8';
        Chart.defaults.font.family = 'Inter, sans-serif';

        const ctxLine = document.getElementById('mainLineChart').getContext('2d');
        
        let gradientGreen = ctxLine.createLinearGradient(0, 0, 0, 300);
        gradientGreen.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        gradientGreen.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: [<?php echo $labels_str; ?>],
                datasets: [{
                    label: 'Temperatura (ºC)',
                    data: [<?php echo $data_temp_str; ?>],
                    borderColor: '#10b981',
                    backgroundColor: gradientGreen,
                    borderWidth: 3,
                    pointBackgroundColor: '#1e293b',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(51, 65, 85, 0.5)', drawBorder: false },
                        beginAtZero: false
                    },
                    x: {
                        grid: { display: false, drawBorder: false }
                    }
                }
            }
        });

        const ctxDoughnut = document.getElementById('sensorDoughnutChart').getContext('2d');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Online', 'Offline'],
                datasets: [{
                    data: [<?php echo $sensores_online; ?>, <?php echo $sensores_offline; ?>],
                    backgroundColor: ['#10b981', '#ef4444'], // Verde e Vermelho
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 20, color: '#f8fafc' } }
                }
            }
        });
    </script>
</body>
</html>