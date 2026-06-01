<?php
session_start();
// Proteção de página: Se não tiver logado, volta para o login
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
    <title>Sensores - FarmSmart OS</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <div class="app-layout">

        <?php include 'scripts/sidebar.php'; ?>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main-wrapper">
            
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="page-title">Gestão de Sensores</h1>
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
                    
                    <div class="page-header">
                        <h2 class="page-title">Sensores</h2>
                    </div>

                    <div class="sensor-grid">
                        
                        <div class="card sensor-card">
                            <div class="sensor-header">
                                <div class="sensor-icon pulse-green"><i class="fa-solid fa-chart-area"></i></div>
                                <span class="status-dot green">online</span>
                            </div>
                            <div class="sensor-body">
                                <h3>Sensor de Humidade Solo #1</h3>
                                <p>Humidade</p>
                                <div class="sensor-value">65%</div>
                            </div>
                            <div class="sensor-footer">
                                <i class="fa-regular fa-clock"></i> Agora mesmo
                            </div>
                        </div>

                        <div class="card sensor-card">
                            <div class="sensor-header">
                                <div class="sensor-icon pulse-green"><i class="fa-solid fa-chart-area"></i></div>
                                <span class="status-dot green">online</span>
                            </div>
                            <div class="sensor-body">
                                <h3>Sensor de Temperatura #1</h3>
                                <p>Temperatura</p>
                                <div class="sensor-value">24°C</div>
                            </div>
                            <div class="sensor-footer">
                                <i class="fa-regular fa-clock"></i> Há 2 min
                            </div>
                        </div>

                        <div class="card sensor-card offline">
                            <div class="sensor-header">
                                <div class="sensor-icon pulse-grey"><i class="fa-solid fa-chart-area"></i></div>
                                <span class="status-dot red">offline</span>
                            </div>
                            <div class="sensor-body">
                                <h3>Sensor de Chuva Exterior</h3>
                                <p>Chuva</p>
                                <div class="sensor-value">--</div>
                            </div>
                            <div class="sensor-footer">
                                <i class="fa-regular fa-clock"></i> Há 3 horas
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