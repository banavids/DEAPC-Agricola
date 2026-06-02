<?php
session_start();

// Proteção de página (garante que só quem tem login entra)
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
    <title>Atuadores - FarmSmart OS</title>
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
                    <button class="menu-toggle" id="menuToggle"><i class="fa-solid fa-bars"></i></button>
                    <h1 class="page-title">Controlo de Equipamentos</h1>
                </div>
                <div class="topbar-right">
                    <div class="notifications"><i class="fa-solid fa-bell"></i><span class="badge"></span></div>
                    <div class="user-profile">
                        <div class="avatar"><i class="fa-solid fa-user"></i></div>
                        <span class="user-name">Perfil</span>
                    </div>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content">
                    
                    <div class="page-header" style="margin-bottom: 20px;">
                        <h2 class="page-title">Atuadores</h2>
                    </div>

                    <div class="light-card full-width" style="margin-bottom: 25px; border-left: 4px solid #3b82f6;">
                        <h3 style="margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-flask text-blue"></i> Painel de Controlo MQTT (Simulador)
                        </h3>
                        <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">Estes botões comunicam diretamente com o script Python do Raspberry Pi através do Mosquitto.</p>
                        
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button onclick="enviarComandoMQTT('ABRIR')" class="btn" style="background: #eab308; color: white; padding: 10px 20px; border:none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                                <i class="fa-solid fa-door-open"></i> Abrir Portas Teste
                            </button>
                            
                            <button onclick="enviarComandoMQTT('FECHAR')" class="btn" style="background: #64748b; color: white; padding: 10px 20px; border:none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                                <i class="fa-solid fa-door-closed"></i> Fechar Portas Teste
                            </button>

                            <button onclick="enviarComandoMQTT('LIGAR_REGA')" class="btn" style="background: #3b82f6; color: white; padding: 10px 20px; border:none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                                <i class="fa-solid fa-droplet"></i> Ligar Rega Teste
                            </button>
                            
                            <button onclick="enviarComandoMQTT('DESLIGAR_REGA')" class="btn" style="background: #ef4444; color: white; padding: 10px 20px; border:none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                                <i class="fa-solid fa-droplet-slash"></i> Desligar Rega Teste
                            </button>
                        </div>
                    </div>

                    <div class="atuadores-layout">
                        
                        <div class="atuadores-list">
                            <div class="light-card atuador-card">
                                <div class="atuador-icon active"><i class="fa-solid fa-power-off"></i></div>
                                <div class="atuador-info">
                                    <h3>Bomba Principal de Água</h3>
                                    <p>Casa das Máquinas • Bomba</p>
                                </div>
                                <div class="atuador-actions">
                                    <span class="mode-tag mode-auto"><i class="fa-solid fa-bolt"></i> Auto</span>
                                    <button class="btn-action btn-desligar">Desligar</button>
                                </div>
                            </div>

                            <div class="light-card atuador-card">
                                <div class="atuador-icon inactive"><i class="fa-solid fa-power-off"></i></div>
                                <div class="atuador-info">
                                    <h3>Injetor de Fertilizante A</h3>
                                    <p>Estufa 1 • Fertilização</p>
                                </div>
                                <div class="atuador-actions">
                                    <span class="mode-tag mode-manual"><i class="fa-solid fa-hand"></i> Manual</span>
                                    <button class="btn-action btn-ligar">Ligar</button>
                                </div>
                            </div>

                            <div class="light-card atuador-card">
                                <div class="atuador-icon active"><i class="fa-solid fa-power-off"></i></div>
                                <div class="atuador-info">
                                    <h3>Ventiladores Extratores</h3>
                                    <p>Ventilação</p>
                                </div>
                                <div class="atuador-actions">
                                    <span class="mode-tag mode-auto"><i class="fa-solid fa-bolt"></i> Auto</span>
                                    <button class="btn-action btn-desligar">Desligar</button>
                                </div>
                            </div>
                        </div>

                        <div class="light-card logs-panel">
                            <h3 class="logs-title"><i class="fa-solid fa-clock-rotate-left"></i> Logs de Atuadores (Hoje)</h3>
                            
                            <div class="log-timeline">
                                <div class="log-item">
                                    <div class="log-time">14:40:05</div>
                                    <div class="log-details">
                                        <div class="log-main"><span class="tag-on">[ON]</span> Ventiladores Extratores</div>
                                        <div class="log-sub">Origem: Automação (Temp > 26°C)</div>
                                    </div>
                                </div>

                                <div class="log-item">
                                    <div class="log-time">14:32:10</div>
                                    <div class="log-details">
                                        <div class="log-main"><span class="tag-off">[OFF]</span> Bomba Principal de Água</div>
                                        <div class="log-sub">Origem: Manual (Admin)</div>
                                    </div>
                                </div>

                                <div class="log-item">
                                    <div class="log-time">10:00:00</div>
                                    <div class="log-details">
                                        <div class="log-main"><span class="tag-modo">[MODO]</span> Injetor de Fertilizante A</div>
                                        <div class="log-sub">Alterado para: Manual</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Lógica do Menu Lateral Mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('active');
        });
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('open');
            this.classList.remove('active');
        });

        // Função mágica que envia o comando para o PHP e depois para o Python
        function enviarComandoMQTT(comandoDesejado) {
        fetch('scripts/enviar_comando.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ comando: comandoDesejado })
        })
        .then(response => response.json())
            .then(data => {
                if(data.sucesso) {
                    console.log("Sucesso: " + data.mensagem);
                    alert("📡 Sucesso: Comando '" + comandoDesejado + "' enviado para o Raspberry Pi!");
                } else {
                    console.error("Erro: " + data.mensagem);
                    alert("❌ Erro: " + data.mensagem);
                }
            })
            .catch(error => {
                console.error('Erro de rede:', error);
                alert("❌ Erro crítico de comunicação com o servidor.");
            });
        }
    </script>
</body>
</html>