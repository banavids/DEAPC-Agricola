<?php
session_start();
// Proteção: Se não houver sessão ativa, manda para o login
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitorização Live - FarmSmart OS</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <div class="app-layout">

    <?php 
            require_once 'scripts/sidebar.php'; 
            require_once 'scripts/database.php';
    ?>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main-wrapper">
            
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="page-title">Monitorização Live</h1>
                </div>
                
                <div class="topbar-right">
                    <div class="notifications">
                        <i class="fa-solid fa-bell"></i>
                        <span class="badge"></span>
                    </div>
                    <div class="user-profile">
                        <div class="avatar"><i class="fa-solid fa-user"></i></div>
                        <span class="user-name">Gestor</span>
                    </div>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content">
                    
                    <div class="live-header-bar">
                        <h2 class="page-title"><i class="fa-solid fa-bolt text-green"></i> Monitorização em Tempo Real</h2>
                        <div class="websocket-status">
                            <span class="status-dot green"></span> Conexão WebSocket Ativa
                        </div>
                    </div>

                    <div class="kpi-grid live-kpis">
                        <div class="card live-kpi-card">
                            <span class="live-label">HUMIDADE SOLO</span>
                            <span class="live-value text-blue">64.2%</span>
                        </div>
                        <div class="card live-kpi-card">
                            <span class="live-label">TEMPERATURA</span>
                            <span class="live-value text-orange">23.8°C</span>
                        </div>
                        <div class="card live-kpi-card">
                            <span class="live-label">LUMINOSIDADE</span>
                            <span class="live-value text-yellow">842 lx</span>
                        </div>
                        <div class="card live-kpi-card card-glow-green">
                            <span class="live-label">BOMBA PRINCIPAL</span>
                            <span class="live-value text-green">LIGADA</span>
                        </div>
                    </div>

                    <div class="card grafana-container area-live-chart">
                        <h3 class="live-chart-title">LIVE DATA STREAM</h3>
                        <div class="grafana-placeholder">Gráfico Grafana: Live Data Stream (Humidade, Temp, etc.)</div>
                    </div>

                    <div class="live-alerts-box">
                        <h3 class="alert-title"><i class="fa-solid fa-triangle-exclamation"></i> ALERTAS EM TEMPO REAL</h3>
                        <div class="alert-item">
                            <span class="alert-time">14:42:01</span>
                            <span class="alert-message">Nível de água no reservatório B abaixo dos 20%</span>
                        </div>
                    </div>

                </div>
            </main> </div> </div> <script>
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