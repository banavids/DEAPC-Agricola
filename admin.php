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
    <div class="dashboard-content">
    
    <div class="kpi-grid kpi-admin">
        <div class="card kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">BROKER MQTT</span>
                <span class="kpi-value">Online</span>
                <span class="kpi-trend text-green"><i class="fa-solid fa-arrow-trend-up"></i> 99.9% Uptime</span>
            </div>
            <div class="kpi-icon icon-green"><i class="fa-solid fa-server"></i></div>
        </div>
        
        <div class="card kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">TRÁFEGO (MSGS/S)</span>
                <span class="kpi-value">1,245</span>
                <span class="kpi-trend text-blue"><i class="fa-solid fa-arrow-trend-up"></i> 12% vs ontem</span>
            </div>
            <div class="kpi-icon icon-blue"><i class="fa-solid fa-chart-line"></i></div>
        </div>

        <div class="card kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">SENSORES ATIVOS</span>
                <span class="kpi-value">142/145</span>
                <span class="kpi-trend text-red"><i class="fa-solid fa-arrow-trend-down"></i> 3 offline</span>
            </div>
            <div class="kpi-icon icon-purple"><i class="fa-solid fa-microchip"></i></div>
        </div>

        <div class="card kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">CONSUMO TOTAL</span>
                <span class="kpi-value">4.2 kW</span>
                <span class="kpi-trend text-yellow"><i class="fa-solid fa-arrow-trend-up"></i> 5% pico diário</span>
            </div>
            <div class="kpi-icon icon-yellow"><i class="fa-solid fa-bolt"></i></div>
        </div>

        <div class="card kpi-card card-alert">
            <div class="kpi-info">
                <span class="kpi-label text-red">ALERTAS CRÍTICOS</span>
                <span class="kpi-value text-red">3</span>
                <span class="kpi-trend text-red" style="margin-top: 10px;">Bomba 2 - Falha de Pressão</span>
            </div>
            <div class="kpi-icon icon-red-solid"><i class="fa-solid fa-triangle-exclamation"></i></div>
        </div>
    </div>

    <div class="grafana-grid admin-charts">
        
        <div class="card grafana-container area-main-chart">
            <div class="grafana-placeholder">Gráfico Grafana: Tráfego MQTT & Latência (24H)</div>
        </div>
        <div class="card grafana-container area-">

        </div>
    </div>