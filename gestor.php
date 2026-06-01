<?php
session_start();
// Proteção: Se não houver sessão ativa, manda para o login
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FarmSmart OS</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'scripts/sidebar.php'; ?>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="main-wrapper">
        
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 class="page-title">Dashboard Operacional</h1>
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
    
    <div class="action-bar">
        <div class="action-group-left">
            <button class="btn btn-blue"><i class="fa-solid fa-play"></i> Iniciar Rega Manual</button>
            <button class="btn btn-red-solid"><i class="fa-regular fa-square"></i> Desligar Bombas</button>
        </div>
        <div class="action-group-right">
            <button class="btn btn-green"><i class="fa-solid fa-plus"></i> Nova Produção</button>
        </div>
    </div>

    <div class="kpi-grid kpi-gestor">
        <div class="card kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">CULTURAS ATIVAS</span>
                <span class="kpi-value">12</span>
            </div>
            <div class="kpi-icon icon-green"><i class="fa-solid fa-seedling"></i></div>
        </div>
        <div class="card kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">REGAS ATIVAS</span>
                <span class="kpi-value">3</span>
            </div>
            <div class="kpi-icon icon-blue"><i class="fa-solid fa-droplet"></i></div>
        </div>
        <div class="card kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">SENSORES ONLINE</span>
                <span class="kpi-value">145</span>
            </div>
            <div class="kpi-icon icon-purple"><i class="fa-solid fa-microchip"></i></div>
        </div>
        <div class="card kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">ALERTAS</span>
                <span class="kpi-value">2</span>
            </div>
            <div class="kpi-icon icon-red-solid"><i class="fa-solid fa-circle-exclamation"></i></div>
        </div>
    </div>

    <div class="tables-grid">
        <div class="card table-card">
            <h3 class="card-title">Tarefas Pendentes</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tarefa</th>
                        <th>Responsável</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Adubar setor A (Tomates)</td>
                        <td>João Silva</td>
                        <td><span class="status-badge badge-warning">Pendente</span></td>
                    </tr>
                    <tr>
                        <td>Verificar bomba principal</td>
                        <td>Maria Costa</td>
                        <td><span class="status-badge badge-info">Em progresso</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card table-card">
            <h3 class="card-title">Últimas Leituras de Sensores</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Local</th>
                        <th>Humidade</th>
                        <th>Temperatura</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Estufa 1 - Tomates</td>
                        <td>65%</td>
                        <td>24°C</td>
                    </tr>
                    <tr>
                        <td>Setor B - Alfaces</td>
                        <td class="text-danger">42% <i class="fa-solid fa-circle-info"></i></td>
                        <td>22°C</td>
                    </tr>
                    <tr>
                        <td>Setor C - Morangos</td>
                        <td>71%</td>
                        <td>21°C</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</div>

</div>
</body>