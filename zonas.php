<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}

// LÓGICA PARA GRAVAR NOVA ZONA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_zona'])) {
    try {
        $db = new SQLite3(__DIR__ . '/bd/FarmOS.db');
        
        // Agora incluímos o ZON_tipo que tu já tinhas na BD!
        $stmt = $db->prepare("INSERT INTO tblZona (ZON_nome, ZON_tipo, ZON_descricao, ZON_topico_base) VALUES (:nome, :tipo, :desc, :topico)");
        $stmt->bindValue(':nome', $_POST['nome_zona'], SQLITE3_TEXT);
        $stmt->bindValue(':tipo', $_POST['tipo_zona'], SQLITE3_TEXT);
        $stmt->bindValue(':desc', $_POST['desc_zona'], SQLITE3_TEXT);
        
        $topico = strtolower(trim($_POST['topico_zona']));
        $stmt->bindValue(':topico', $topico, SQLITE3_TEXT);
        
        $stmt->execute();
        $db->close();
        
        header("Location: zonas.php");
        exit;
    } catch (Exception $e) {
        $erro = "Erro ao guardar zona: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zonas - FarmSmart OS</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        <?php require_once 'scripts/sidebar.php'; ?>
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle"><i class="fa-solid fa-bars"></i></button>
                    <h1 class="page-title">Gestão de Zonas</h1>
                </div>
            </header>

            <main class="content-body">
                <div class="dashboard-content">
                    
                    <?php if(isset($erro)) echo "<div style='color: red; padding: 10px; background: #fee2e2; margin-bottom: 20px; border-radius: 4px;'>$erro</div>"; ?>

                    <div class="light-card" style="margin-bottom: 30px; border-top: 4px solid #8b5cf6;">
                        <h3 style="margin-bottom: 15px;"><i class="fa-solid fa-map-location-dot"></i> Registar Nova Zona</h3>
                        
                        <form method="POST" action="zonas.php" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                            
                            <div style="flex-grow: 1; min-width: 200px;">
                                <label style="font-size: 13px; color: #64748b; margin-bottom: 5px; display:block;">Nome da Zona</label>
                                <input type="text" name="nome_zona" placeholder="Ex: Estufa Principal" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                            </div>
                            
                            <div style="flex-grow: 1; min-width: 150px;">
                                <label style="font-size: 13px; color: #64748b; margin-bottom: 5px; display:block;">Tipo de Zona</label>
                                <select name="tipo_zona" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                                    <option value="Estufa">Estufa</option>
                                    <option value="Campo Aberto">Campo Aberto</option>
                                    <option value="Armazém">Armazém</option>
                                    <option value="Casa das Máquinas">Casa das Máquinas</option>
                                </select>
                            </div>
                            
                            <div style="flex-grow: 2; min-width: 200px;">
                                <label style="font-size: 13px; color: #64748b; margin-bottom: 5px; display:block;">Descrição</label>
                                <input type="text" name="desc_zona" placeholder="Ex: Produção de morangos" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                            </div>

                            <div style="flex-grow: 1; min-width: 200px;">
                                <label style="font-size: 13px; color: #64748b; margin-bottom: 5px; display:block;">Tópico MQTT Base</label>
                                <input type="text" name="topico_zona" placeholder="Ex: farmsmart/estufa1" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-family: monospace;">
                            </div>

                            <button type="submit" class="btn" style="background: #8b5cf6; color: white; padding: 9px 20px; border:none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                Inserir Zona
                            </button>
                        </form>
                    </div>

                    <h2 class="page-title" style="margin-bottom: 20px;">Zonas Ativas</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                        
                        <?php
                        try {
                            $db = new SQLite3(__DIR__ . '/bd/FarmOS.db');
                            $resultados = $db->query("SELECT * FROM tblZona ORDER BY ZON_id ASC");
                            
                            while ($row = $resultados->fetchArray(SQLITE3_ASSOC)) {
                                // Ícone dinâmico baseado no teu ZON_tipo!
                                $icone = "fa-map-pin";
                                if($row['ZON_tipo'] == 'Estufa') $icone = "fa-seedling";
                                if($row['ZON_tipo'] == 'Armazém') $icone = "fa-boxes-stacked";
                                if($row['ZON_tipo'] == 'Casa das Máquinas') $icone = "fa-gears";
                                
                                // Estado default caso os antigos estejam a null
                                $estado = $row['ZON_estado'] ?? 'Ativa';
                                $topico = $row['ZON_topico_base'] ?? 'Sem Tópico MQTT';

                                echo "
                                <div class='light-card' style='position: relative; border-left: 4px solid #8b5cf6;'>
                                    <div style='position: absolute; top: 15px; right: 15px;'>
                                        <span style='background: #dcfce7; color: #16a34a; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold;'>{$estado}</span>
                                    </div>
                                    <h3 style='margin-bottom: 5px; font-size: 18px; padding-right: 50px;'>
                                        <i class='fa-solid {$icone}' style='color: #8b5cf6; margin-right: 8px;'></i> {$row['ZON_nome']}
                                    </h3>
                                    
                                    <span style='display: inline-block; background: #e2e8f0; color: #475569; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-bottom: 10px;'>
                                        {$row['ZON_tipo']}
                                    </span>
                                    
                                    <p style='font-size: 13px; color: #64748b; margin-bottom: 15px; min-height: 20px;'>
                                        {$row['ZON_descricao']}
                                    </p>
                                    
                                    <div style='background: #f8fafc; padding: 10px; border-radius: 4px; border: 1px dashed #cbd5e1;'>
                                        <p style='font-size: 11px; color: #64748b; margin-bottom: 3px; text-transform: uppercase;'>Canal MQTT (Identificador)</p>
                                        <code style='color: #8b5cf6; font-weight: bold; font-size: 13px;'>{$topico}</code>
                                    </div>
                                </div>";
                            }
                            $db->close();
                        } catch (Exception $e) {
                            echo "Erro a carregar zonas: " . $e->getMessage();
                        }
                        ?>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementB    yId('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('active');
        });
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('open');
            this.classList.remove('active');
        });
    </script>
</body>
</html>