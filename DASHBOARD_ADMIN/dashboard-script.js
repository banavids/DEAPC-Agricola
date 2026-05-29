/* ========================================================================== */
/* FarmSmart OS - Dashboard JavaScript */
/* ========================================================================== */

// Menu Toggle (Mobile)
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.querySelector('.sidebar');

if (menuToggle) {
    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
    });

    // Close sidebar when clicking outside
    document.addEventListener('click', function(e) {
        if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    });
}

/* ========================================================================== */
/* Charts Configuration */
/* ========================================================================== */

// Configuração global de cores
Chart.defaults.color = '#94a3b8';
Chart.defaults.borderColor = '#1f2937';
Chart.defaults.font.family = "'Inter', sans-serif";

// Traffic Chart (Linha)
const trafficCtx = document.getElementById('trafficChart');
if (trafficCtx) {
    new Chart(trafficCtx, {
        type: 'line',
        data: {
            labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
            datasets: [
                {
                    label: 'Mensagens/min',
                    data: [120, 200, 350, 480, 420, 280, 150],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7
                },
                {
                    label: 'Latência (ms)',
                    data: [15, 18, 22, 25, 20, 16, 12],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        color: 'rgba(31, 41, 55, 0.3)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        drawOnChartArea: false,
                        color: 'rgba(31, 41, 55, 0.3)'
                    }
                },
                x: {
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        color: 'rgba(31, 41, 55, 0.3)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Environmental Chart (Barras)
const environmentCtx = document.getElementById('environmentChart');
if (environmentCtx) {
    new Chart(environmentCtx, {
        type: 'bar',
        data: {
            labels: ['Zona A', 'Zona B', 'Zona C', 'Zona D'],
            datasets: [
                {
                    label: 'Temperatura (°C)',
                    data: [22, 24, 18, 26],
                    backgroundColor: '#f59e0b',
                    borderColor: '#d97706',
                    borderWidth: 1,
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'x',
            scales: {
                y: {
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        color: 'rgba(31, 41, 55, 0.3)'
                    }
                },
                x: {
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        color: 'rgba(31, 41, 55, 0.3)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#94a3b8',
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
}

// Energy Distribution Chart (Circular)
const energyCtx = document.getElementById('energyChart');
if (energyCtx) {
    new Chart(energyCtx, {
        type: 'doughnut',
        data: {
            labels: ['Abastecimento', 'Iluminação', 'Climatização', 'Outros'],
            datasets: [
                {
                    data: [35, 25, 28, 12],
                    backgroundColor: [
                        '#3b82f6',
                        '#f59e0b',
                        '#10b981',
                        '#8b5cf6'
                    ],
                    borderColor: '#131927',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        color: '#94a3b8',
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
}

/* ========================================================================== */
/* Circular Progress Charts */
/* ========================================================================== */

function drawCircularProgress(canvasId, percentage, color) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const radius = Math.min(canvas.width, canvas.height) / 2;
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const lineWidth = 8;

    // Limpar canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Background circle
    ctx.strokeStyle = 'rgba(31, 41, 55, 0.3)';
    ctx.lineWidth = lineWidth;
    ctx.beginPath();
    ctx.arc(centerX, centerY, radius - lineWidth / 2, 0, 2 * Math.PI);
    ctx.stroke();

    // Progress circle
    const angle = (percentage / 100) * 2 * Math.PI - Math.PI / 2;
    ctx.strokeStyle = color;
    ctx.lineWidth = lineWidth;
    ctx.lineCap = 'round';
    ctx.beginPath();
    ctx.arc(centerX, centerY, radius - lineWidth / 2, -Math.PI / 2, angle);
    ctx.stroke();
}

// Draw all circular progress charts
drawCircularProgress('cpuChart', 45, '#10b981');
drawCircularProgress('ramChart', 78, '#f59e0b');
drawCircularProgress('storageChart', 32, '#10b981');
drawCircularProgress('networkChart', 92, '#ef4444');

/* ========================================================================== */
/* Simulação de Dados em Tempo Real (Opcional) */
/* ========================================================================== */

// Atualizar dados periodicamente (simulação)
function updateDashboardData() {
    // Simular mudança de valores
    const trafficValue = Math.floor(Math.random() * 100) + 1200;
    const cpuLoad = Math.floor(Math.random() * 60) + 20;
    const ramUsage = Math.floor(Math.random() * 40) + 60;

    // Atualizar no DOM (se necess§rio)
    // console.log(`Traffic: ${trafficValue}, CPU: ${cpuLoad}%, RAM: ${ramUsage}%`);
}

// Chamar a cada 10 segundos (opcional)
// setInterval(updateDashboardData, 10000);

/* ========================================================================== */
/* Notificações e Interatividade */
/* ========================================================================== */

// Notification button click
const notificationBtn = document.querySelector('.notification-btn');
if (notificationBtn) {
    notificationBtn.addEventListener('click', function() {
        alert('Você tem 2 notificações novas!');
        // Aqui você pode abrir um painel de notificações
    });
}

// Active nav item
const navItems = document.querySelectorAll('.nav-item');
navItems.forEach(item => {
    item.addEventListener('click', function(e) {
        navItems.forEach(i => i.classList.remove('active'));
        this.classList.add('active');
    });
});

/* ========================================================================== */
/* Tooltip / Informações Adicionais */
/* ========================================================================== */

// Adicionar informação ao passar o mouse nas cards
const statCards = document.querySelectorAll('.stat-card');
statCards.forEach(card => {
    card.addEventListener('mouseenter', function() {
        // Você pode adicionar animações ou mostrar info adicional
        this.style.transform = 'translateY(-4px)';
    });

    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

/* ========================================================================== */
/* Search Functionality */
/* ========================================================================== */

const searchInput = document.querySelector('.search-box input');
if (searchInput) {
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                console.log('Procurando:', query);
                // Implementar busca real aqui
            }
        }
    });
}

/* ========================================================================== */
/* Página Carregada */
/* ========================================================================== */

console.log('FarmSmart OS - Dashboard Carregado');
console.log('Bem-vindo ao Dashboard de Administrador');
