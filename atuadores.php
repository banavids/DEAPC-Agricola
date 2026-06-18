<?php
session_start();
require_once 'scripts/database.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}

if ($_SESSION['user_group'] == 3) {
    header("Location: operador.php"); 
    exit;
}

$atuadores_db = db_select("SELECT ATU_tipo, ATU_estado FROM tblAtuador");

$estados = [
    'rega' => 'desligado',
    'porta' => 'desligado'
];

if ($atuadores_db) {
    foreach ($atuadores_db as $atuador) {
        $estados[$atuador['ATU_tipo']] = strtolower($atuador['ATU_estado']); // Força minúsculas para evitar erros
    }
}

$rega_estado = $estados['rega'];
$rega_btn_class = ($rega_estado === 'ligado') ? 'btn-on' : 'btn-off';
$rega_btn_text = ($rega_estado === 'ligado') ? '<i class="fa-solid fa-power-off"></i> Desligar Rega' : '<i class="fa-solid fa-power-off"></i> Ligar Rega';
$rega_icon_class = ($rega_estado === 'ligado') ? 'fa-solid fa-droplet icon-on' : 'fa-solid fa-droplet icon-off';

$porta_estado = $estados['porta'];
$porta_btn_class = ($porta_estado === 'ligado') ? 'btn-on' : 'btn-off';
$porta_btn_text = ($porta_estado === 'ligado') ? '<i class="fa-solid fa-door-closed"></i> Fechar Portas' : '<i class="fa-solid fa-door-open"></i> Abrir Portas';
$porta_icon_class = ($porta_estado === 'ligado') ? 'fa-solid fa-door-open icon-on' : 'fa-solid fa-door-closed icon-off';

?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atuadores - FarmSmart OS</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .btn-toggle { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; transition: all 0.3s; color: white; min-width: 120px; }
        .btn-on { background-color: #ef4444; } 
        .btn-off { background-color: #10b981; } 
        .btn-toggle:disabled { opacity: 0.7; cursor: not-allowed; }
        .icon-on { color: #10b981; text-shadow: 0 0 10px rgba(16, 185, 129, 0.4); }
        .icon-off { color: #64748b; }
    </style>
</head>
<body>
    
    <div class="app-layout">
        <?php include 'scripts/sidebar.php'; ?>
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle"><i class="fa-solid fa-bars"></i></button>
                    <h1 class="page-title">Controlo de Equipamentos</h1>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content">
                    
                    <div class="page-header" style="margin-bottom: 20px;">
                        <h2 class="page-title">Atuadores</h2>
                        <p style="color: #64748b; font-size: 14px;">Controlo direto via MQTT para o Raspberry Pi.</p>
                    </div>

                    <div class="atuadores-layout">
                        <div class="atuadores-list">
                            <div class="light-card atuador-card">
                                <div class="atuador-icon"><i id="icon-rega" class="<?php echo $rega_icon_class; ?>" style="font-size: 24px;"></i></div>
                                <div class="atuador-info">
                                    <h3>Bomba Principal de Água</h3>
                                    <p>Estufa 1 • Sistema de Rega</p>
                                </div>
                                <div class="atuador-actions">
                                    <span class="mode-tag mode-manual"><i class="fa-solid fa-hand"></i> Manual</span>
                                    <button id="btn-rega" class="btn-toggle <?php echo $rega_btn_class; ?>" data-tipo="rega" data-estado="<?php echo $rega_estado; ?>" onclick="toggleAtuador(this)">
                                        <?php echo $rega_btn_text; ?>
                                    </button>
                                </div>
                            </div>

                            <div class="light-card atuador-card">
                                <div class="atuador-icon"><i id="icon-porta" class="<?php echo $porta_icon_class; ?>" style="font-size: 24px;"></i></div>
                                <div class="atuador-info">
                                    <h3>Portas de Ventilação</h3>
                                    <p>Estufa 1 • Acessos</p>
                                </div>
                                <div class="atuador-actions">
                                    <span class="mode-tag mode-manual"><i class="fa-solid fa-hand"></i> Manual</span>
                                    <button id="btn-porta" class="btn-toggle <?php echo $porta_btn_class; ?>" data-tipo="porta" data-estado="<?php echo $porta_estado; ?>" onclick="toggleAtuador(this)">
                                        <?php echo $porta_btn_text; ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="scripts/atuadores.js"></script>
</body>
</html>