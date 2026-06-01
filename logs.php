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
    <title>Auditoria - FarmSmart OS</title>
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
                    <h1 class="page-title">Auditoria e Logs</h1>
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
                        <h2 class="page-title">Logs e Auditoria</h2>
                    </div>

                    <div class="light-card full-width">
                        <h2 class="page-title">Logs e Auditoria</h2>
                        <button class="btn-outline">Filtros</button>
                        <div class="toolbar" style="margin-bottom: 20px;">
                            <div class="search-box">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" placeholder="Procurar nos logs...">
                            </div>
                            <button class="btn-outline"><i class="fa-solid fa-filter"></i> Filtros (Data, Utilizador)</button>
                        </div>

                        <div class="table-responsive">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>Data / Hora</th>
                                        <th>Utilizador / Ator</th>
                                        <th>Ação Realizada</th>
                                        <th>Origem / Detalhes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="td-sub text-dark">21 Mai 2026 14:42</span></td>
                                        <td><span class="td-title text-sm">Sistema Automático</span></td>
                                        <td><span class="td-sub text-dark">Ligou Bomba Principal</span></td>
                                        <td><span class="td-sub">Regra: Humidade < 40%</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="td-sub text-dark">21 Mai 2026 10:15</span></td>
                                        <td><span class="td-title text-sm">Admin Silva</span></td>
                                        <td><span class="td-sub text-dark">Criou utilizador 'Carlos Técnico'</span></td>
                                        <td><span class="td-sub">Interface Web</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="td-sub text-dark">21 Mai 2026 09:30</span></td>
                                        <td><span class="td-title text-sm">Maria Costa</span></td>
                                        <td><span class="td-sub text-dark">Alterou configuração de rega</span></td>
                                        <td><span class="td-sub">App Mobile</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="td-sub text-dark">20 Mai 2026 18:45</span></td>
                                        <td><span class="td-title text-sm">João Operário</span></td>
                                        <td><span class="td-sub text-dark">Concluiu Tarefa 'Colheita Lote 4'</span></td>
                                        <td><span class="td-sub">App Mobile</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="td-sub text-dark">20 Mai 2026 16:00</span></td>
                                        <td><span class="td-title text-sm">Sistema Automático</span></td>
                                        <td><span class="td-sub text-red fw-bold">Alerta Crítico: Sensor Offline S001</span></td>
                                        <td><span class="td-sub">Monitor MQTT</span></td>
                                    </tr>
                                </tbody>
                            </table>
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