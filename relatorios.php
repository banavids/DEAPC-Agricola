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
    <title>Relatórios - FarmSmart OS</title>
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
                    <h1 class="page-title">Relatórios e Exportações</h1>
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
                    
                    <div class="page-header" style="margin-bottom: 20px;">
                        <h2 class="page-title">Relatórios</h2>
                        <button class="btn btn-green"><i class="fa-solid fa-file-circle-plus"></i> Gerar Novo Relatório</button>
                    </div>

                    <div class="reports-layout">
                        
                        <div class="light-card reports-sidebar">
                            <h3 class="sidebar-title">Tipos de Relatório</h3>
                            <ul class="reports-menu">
                                <li class="active">Produção</li>
                                <li>Consumo de Água</li>
                                <li>Dados de Sensores</li>
                                <li>Tarefas de Operários</li>
                            </ul>
                        </div>

                        <div class="reports-list">
                            
                            <div class="light-card report-item">
                                <div class="report-icon bg-green-light"><i class="fa-solid fa-chart-column text-green"></i></div>
                                <div class="report-info">
                                    <h3>Rendimento de Colheitas (Mensal)</h3>
                                    <p><i class="fa-regular fa-file-lines"></i> Produção &nbsp;|&nbsp; <i class="fa-regular fa-calendar"></i> Nov 2026</p>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-download btn-csv"><i class="fa-solid fa-download"></i> CSV</button>
                                    <button class="btn-download btn-pdf"><i class="fa-solid fa-download"></i> PDF</button>
                                </div>
                            </div>

                            <div class="light-card report-item">
                                <div class="report-icon bg-green-light"><i class="fa-solid fa-chart-column text-green"></i></div>
                                <div class="report-info">
                                    <h3>Consumo de Água Global</h3>
                                    <p><i class="fa-solid fa-droplet"></i> Água &nbsp;|&nbsp; <i class="fa-regular fa-calendar"></i> Últimos 30 dias</p>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-download btn-csv"><i class="fa-solid fa-download"></i> CSV</button>
                                    <button class="btn-download btn-pdf"><i class="fa-solid fa-download"></i> PDF</button>
                                </div>
                            </div>

                            <div class="light-card report-item">
                                <div class="report-icon bg-green-light"><i class="fa-solid fa-chart-column text-green"></i></div>
                                <div class="report-info">
                                    <h3>Médias de Temperatura - Estufa 1</h3>
                                    <p><i class="fa-solid fa-temperature-half"></i> Sensores &nbsp;|&nbsp; <i class="fa-regular fa-calendar"></i> Semana 42</p>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-download btn-csv"><i class="fa-solid fa-download"></i> CSV</button>
                                    <button class="btn-download btn-pdf"><i class="fa-solid fa-download"></i> PDF</button>
                                </div>
                            </div>

                            <div class="light-card report-item">
                                <div class="report-icon bg-green-light"><i class="fa-solid fa-chart-column text-green"></i></div>
                                <div class="report-info">
                                    <h3>Alertas Críticos e Falhas de Equipamento</h3>
                                    <p><i class="fa-solid fa-triangle-exclamation"></i> Sistema &nbsp;|&nbsp; <i class="fa-regular fa-calendar"></i> Trimestre Q3</p>
                                </div>
                                <div class="report-actions">
                                    <button class="btn-download btn-csv"><i class="fa-solid fa-download"></i> CSV</button>
                                    <button class="btn-download btn-pdf"><i class="fa-solid fa-download"></i> PDF</button>
                                </div>
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