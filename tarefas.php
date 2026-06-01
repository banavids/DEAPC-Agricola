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
    <title>Tarefas e Manutenção - FarmSmart OS</title>
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
                    <h1 class="page-title">Gestão de Tarefas</h1>
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
                        <h2 class="page-title">Tarefas e Manutenção</h2>
                        <button class="btn btn-green"><i class="fa-solid fa-plus"></i> Atribuir Tarefa</button>
                    </div>

                    <div class="light-card full-width">
                        <div class="table-responsive">
                            <table class="modern-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Descrição da Tarefa</th>
                                        <th>Produção</th>
                                        <th>Responsável</th>
                                        <th>Prioridade</th>
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="td-title">Manutenção Preventiva Bomba Principal</span></td>
                                        <td><span class="td-sub">Todas</span></td>
                                        <td><span class="td-sub">Carlos</span></td>
                                        <td><span class="pill-badge bg-red-light text-red">Alta</span></td>
                                        <td><span class="status-text text-yellow"><i class="fa-regular fa-circle"></i> Pendente</span></td>
                                        <td class="text-right"><button class="btn-icon"><i class="fa-solid fa-ellipsis"></i></button></td>
                                    </tr>
                                    <tr>
                                        <td><span class="td-title">Colheita Lote 4</span></td>
                                        <td><span class="td-sub">Estufa 2 - Tomates</span></td>
                                        <td><span class="td-sub">Maria</span></td>
                                        <td><span class="pill-badge bg-blue-light text-blue">Normal</span></td>
                                        <td><span class="status-text text-blue"><i class="fa-solid fa-arrows-rotate"></i> Em Progresso</span></td>
                                        <td class="text-right"><button class="btn-icon"><i class="fa-solid fa-ellipsis"></i></button></td>
                                    </tr>
                                    <tr class="row-muted">
                                        <td><span class="td-title">Calibração Sensor PH</span></td>
                                        <td><span class="td-sub">Setor Hidroponia</span></td>
                                        <td><span class="td-sub">Carlos</span></td>
                                        <td><span class="pill-badge bg-blue-light text-blue">Normal</span></td>
                                        <td><span class="status-text text-green"><i class="fa-regular fa-circle-check"></i> Concluído</span></td>
                                        <td class="text-right"><button class="btn-icon"><i class="fa-solid fa-ellipsis"></i></button></td>
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