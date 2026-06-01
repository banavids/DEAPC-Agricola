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
    <title>MQTT & IoT Hub - FarmSmart OS</title>
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
                    <h1 class="page-title">Hub IoT / MQTT</h1>
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
                        <h2 class="page-title">MQTT & IoT Hub</h2>
                        <div class="broker-badge">
                            <i class="fa-solid fa-server"></i> Broker: mqtt.farmsmart.local:1883 <span class="badge-online">ONLINE</span>
                        </div>
                    </div>

                    <div class="mqtt-layout">
                        
                        <div class="card terminal-window">
                            <div class="terminal-header">
                                >_ Monitor de Mensagens / Tópicos
                            </div>
                            <div class="terminal-body">
                                
                                <div class="terminal-block">
                                    <div class="term-topic">Tópico: <span class="text-green">farm/sensor/estufa1/humidade</span></div>
                                    <div class="term-payload">Payload: <span class="text-yellow">{"value": 65.4, "unit": "%", "timestamp": 1679412301}</span></div>
                                </div>

                                <div class="terminal-block">
                                    <div class="term-topic">Tópico: <span class="text-green">farm/sensor/estufa1/temp</span></div>
                                    <div class="term-payload">Payload: <span class="text-yellow">{"value": 24.1, "unit": "C", "timestamp": 1679412302}</span></div>
                                </div>

                                <div class="terminal-block">
                                    <div class="term-topic">Tópico: <span class="text-blue">farm/actuator/bomba1/cmd</span></div>
                                    <div class="term-payload">Payload: <span class="text-white">ON</span></div>
                                </div>

                                <div class="terminal-block">
                                    <div class="term-topic">Tópico: <span class="text-purple">farm/system/status</span></div>
                                    <div class="term-payload">Payload: <span class="text-yellow">{"status": "ok", "uptime": 86400}</span></div>
                                </div>

                            </div>
                        </div>

                        <div class="mqtt-sidebar">
                            
                            <div class="card tree-card">
                                <h3 class="card-title" style="font-size: 12px;"><i class="fa-solid fa-share-nodes text-green"></i> ÁRVORE DE TÓPICOS</h3>
                                <ul class="topic-tree">
                                    <li><i class="fa-solid fa-folder"></i> farm/
                                        <ul>
                                            <li><i class="fa-solid fa-folder"></i> sensor/
                                                <ul>
                                                    <li class="leaf text-green"><i class="fa-regular fa-file"></i> estufa1/humidade</li>
                                                    <li class="leaf text-green"><i class="fa-regular fa-file"></i> estufa1/temp</li>
                                                    <li class="leaf text-green"><i class="fa-regular fa-file"></i> exterior/chuva</li>
                                                </ul>
                                            </li>
                                            <li><i class="fa-solid fa-folder"></i> actuator/
                                                <ul>
                                                    <li class="leaf text-blue"><i class="fa-regular fa-file"></i> bomba1/cmd</li>
                                                    <li class="leaf text-blue"><i class="fa-regular fa-file"></i> bomba1/status</li>
                                                    <li class="leaf text-blue"><i class="fa-regular fa-file"></i> val4/cmd</li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>

                            <div class="card stats-card">
                                <h3 class="card-title" style="font-size: 12px;"><i class="fa-solid fa-chart-simple text-blue"></i> ESTATÍSTICAS DO BROKER</h3>
                                <div class="stat-row">
                                    <span class="stat-label">Dispositivos Ligados</span>
                                    <span class="stat-value">142</span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Msg / Segundo</span>
                                    <span class="stat-value">24.5</span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Total Mensagens (24h)</span>
                                    <span class="stat-value">2.1M</span>
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