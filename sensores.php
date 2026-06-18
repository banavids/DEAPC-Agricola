<?php
session_start();
require_once 'scripts/database.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}

$user_id = $_SESSION['user_id'];

$user_dados = db_select("SELECT USR_nome FROM tblUser WHERE USR_id = :id", [':id' => $user_id]);
$nome_completo = $user_dados ? $user_dados[0]['USR_nome'] : 'Utilizador';

$sensores_atuais = db_select("SELECT * FROM tblSensor ORDER BY SNR_id ASC");


$ultimas_leituras = db_select("SELECT * FROM tblLeituras ORDER BY LEI_data_hora DESC LIMIT 10");

// Função auxiliar para definir cores, ícones e unidades automaticamente com base no tipo
function getSensorConfig($tipo) {
    $tipo = strtolower($tipo);
    if (strpos($tipo, 'temperatura') !== false) {
        return ['icone' => 'fa-temperature-half', 'cor' => '#ef4444', 'unidade' => 'ºC']; 
    } elseif ($tipo === 'humidade') {
        return ['icone' => 'fa-droplet', 'cor' => '#3b82f6', 'unidade' => '%'];
    } elseif ($tipo === 'humidade_solo') {
        return ['icone' => 'fa-seedling', 'cor' => '#f59e0b', 'unidade' => '%']; 
    }
    return ['icone' => 'fa-microchip', 'cor' => '#64748b', 'unidade' => ''];
}

?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="15">
    <title>Sensores em Tempo Real - FarmSmart OS</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sensor-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .sensor-card { text-align: center; padding: 25px 20px; position: relative; overflow: hidden; }
        .sensor-icon-bg { position: absolute; top: -10px; right: -10px; font-size: 100px; opacity: 0.05; }
        .sensor-value { font-size: 2.5rem; font-weight: 700; margin: 15px 0 5px 0; }
        .sensor-unit { font-size: 1.2rem; font-weight: 500; opacity: 0.8; }
        .sensor-status { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; margin-bottom: 10px;}
        .status-online { background-color: rgba(16, 185, 129, 0.2); color: #10b981; }
        .status-offline { background-color: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .update-time { font-size: 12px; color: #64748b; margin-top: 10px; }
    </style>
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
                    <h1 class="page-title">Monitorização de Sensores</h1>
                </div>
                
                <div class="topbar-right">
                    <div class="user-profile">
                        <div class="avatar"><i class="fa-solid fa-user"></i></div>
                        <span class="user-name"><?php echo htmlspecialchars($nome_completo); ?></span> 
                    </div>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content">
                    
                    <div class="page-header" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h2 class="page-title">Estado Atual</h2>
                            <p style="color: #64748b; font-size: 14px;">Atualização automática a cada 15 segundos.</p>
                        </div>
                        <button onclick="window.location.reload();" class="btn" style="background: #3b82f6; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer;">
                            <i class="fa-solid fa-rotate-right"></i> Atualizar Agora
                        </button>
                    </div>

                    <div class="sensor-grid">
                        <?php if (empty($sensores_atuais)): ?>
                            <div class="light-card full-width" style="text-align: center; color: #64748b;">
                                <i class="fa-solid fa-triangle-exclamation" style="font-size: 24px; margin-bottom: 10px;"></i>
                                <p>Nenhum sensor registado na base de dados.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($sensores_atuais as $sensor): 
                                $config = getSensorConfig($sensor['SNR_tipo']);
                                $estado_class = (strtolower($sensor['SNR_estado']) === 'online') ? 'status-online' : 'status-offline';
                            ?>
                                <div class="light-card sensor-card">
                                    <i class="fa-solid <?php echo $config['icone']; ?> sensor-icon-bg" style="color: <?php echo $config['cor']; ?>;"></i>
                                    
                                    <div class="sensor-status <?php echo $estado_class; ?>">
                                        <i class="fa-solid fa-circle" style="font-size: 8px; margin-right: 4px;"></i> <?php echo htmlspecialchars($sensor['SNR_estado']); ?>
                                    </div>
                                    
                                    <h3 style="color: #cbd5e1; font-size: 16px;"><?php echo htmlspecialchars($sensor['SNR_nome']); ?></h3>
                                    
                                    <div class="sensor-value" style="color: <?php echo $config['cor']; ?>;">
                                        <?php echo number_format($sensor['SNR_ultima_leitura'], 1); ?>
                                        <span class="sensor-unit"><?php echo $config['unidade']; ?></span>
                                    </div>
                                    
                                    <div class="update-time">
                                        <i class="fa-regular fa-clock"></i> Lida em: <?php echo date('d/m/Y H:i:s', strtotime($sensor['SNR_data_leitura'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="light-card table-card">
                        <h3 class="card-title" style="margin-bottom: 15px;">
                            <i class="fa-solid fa-list"></i> Últimas 10 Leituras Registadas
                        </h3>
                        <div style="overflow-x: auto;">
                            <table class="data-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #94a3b8;">
                                        <th style="padding: 12px;">ID Leitura</th>
                                        <th style="padding: 12px;">Zona</th>
                                        <th style="padding: 12px;">Tipo de Sensor</th>
                                        <th style="padding: 12px;">Valor</th>
                                        <th style="padding: 12px;">Data / Hora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($ultimas_leituras)): ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 20px; color: #64748b;">Sem dados de histórico.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($ultimas_leituras as $leitura): 
                                            $config_tabela = getSensorConfig($leitura['LEI_tipo_sensor']);
                                        ?>
                                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                                <td style="padding: 12px; color: #64748b;">#<?php echo $leitura['LEI_id']; ?></td>
                                                <td style="padding: 12px;">Zona <?php echo htmlspecialchars($leitura['LEI_zona_id']); ?></td>
                                                <td style="padding: 12px;">
                                                    <i class="fa-solid <?php echo $config_tabela['icone']; ?>" style="color: <?php echo $config_tabela['cor']; ?>; margin-right: 8px;"></i>
                                                    <?php echo ucfirst(htmlspecialchars($leitura['LEI_tipo_sensor'])); ?>
                                                </td>
                                                <td style="padding: 12px; font-weight: bold; color: <?php echo $config_tabela['cor']; ?>;">
                                                    <?php echo number_format($leitura['LEI_valor'], 1) . $config_tabela['unidade']; ?>
                                                </td>
                                                <td style="padding: 12px; font-size: 13px; color: #cbd5e1;">
                                                    <?php echo date('d/m/Y H:i:s', strtotime($leitura['LEI_data_hora'])); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (menuToggle && sidebar && sidebarOverlay) {
                menuToggle.addEventListener('click', () => {
                    sidebar.classList.add('open');
                    sidebarOverlay.classList.add('active');
                });

                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    this.classList.remove('active');
                });
            }
        });
    </script>
</body>
</html>