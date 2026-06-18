<?php
session_start();
require_once 'scripts/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login/login-farmsmart.html");
    exit;
}
if ($_SESSION['user_group'] == 3) {
    header("Location: operador.php");
    exit;
}

function get_val($type) {
    global $db;
    $sql = "SELECT LEI_valor FROM tblLeituras WHERE TRIM(LOWER(LEI_tipo_sensor)) = TRIM(LOWER('$type')) ORDER BY LEI_data_hora DESC LIMIT 1";
    $res = db_select($sql);
    return $res ? (float)$res[0]['LEI_valor'] : 0.0;
}

$val_temp = get_val('temperatura');
$val_hum = get_val('humidade');
$val_solo = get_val('humidade_solo');
$val_ext = get_val('temperatura_exterior');

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Gestor</title>
    <link rel="stylesheet" href="styles/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php require_once 'scripts/sidebar.php'; ?>
    <div class="main-wrapper">
        <main class="content-body">
            
            <div style="background: #2d3748; color: #fff; padding: 10px; margin-bottom: 20px; font-family: monospace;">
                Debug: T=<?php echo $val_temp; ?> | H=<?php echo $val_hum; ?> | Solo=<?php echo $val_solo; ?> | Ext=<?php echo $val_ext; ?>
            </div>

            <div class="card" style="padding: 20px; display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px;">
                <div style="width: 150px;"><canvas id="gaugeTemp"></canvas><h4 style="text-align: center;">Temp. Int.</h4></div>
                <div style="width: 150px;"><canvas id="gaugeHum"></canvas><h4 style="text-align: center;">Hum. Int.</h4></div>
                <div style="width: 150px;"><canvas id="gaugeSolo"></canvas><h4 style="text-align: center;">Hum. Solo</h4></div>
                <div style="width: 150px;"><canvas id="gaugeExt"></canvas><h4 style="text-align: center;">Temp. Ext.</h4></div>
            </div>

            <div class="card table-card" style="margin-top: 20px;">
                <table class="data-table">
                    <thead><tr><th>Sensor</th><th>Valor</th><th>Data/Hora</th></tr></thead>
                    <tbody>
                        <?php 
                        $logs = db_select("SELECT LEI_tipo_sensor, LEI_valor, LEI_data_hora FROM tblLeituras ORDER BY LEI_data_hora DESC LIMIT 5");
                        foreach($logs as $l) echo "<tr><td>{$l['LEI_tipo_sensor']}</td><td>{$l['LEI_valor']}</td><td>{$l['LEI_data_hora']}</td></tr>";
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
    function criarGauge(id, valor, cor, unidade) {

        const v = valor > 0 ? valor : 0.1;
        new Chart(document.getElementById(id), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [v, 100 - v],
                    backgroundColor: [cor, '#1e293b'],
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: {display: false}, tooltip: {enabled: false} }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw(chart) {
                    const { ctx, chartArea: { width, height } } = chart;
                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 20px sans-serif';
                    ctx.fillText(valor + unidade, width / 2, height - 5);
                }
            }]
        });
    }

    criarGauge('gaugeTemp', <?php echo $val_temp; ?>, '#10b981', 'ºC');
    criarGauge('gaugeHum', <?php echo $val_hum; ?>, '#3b82f6', '%');
    criarGauge('gaugeSolo', <?php echo $val_solo; ?>, '#f59e0b', '%');
    criarGauge('gaugeExt', <?php echo $val_ext; ?>, '#6366f1', 'ºC');
    </script>
</body>
</html>