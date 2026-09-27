<?php
$id_perfil = isset($id_perfil) ? intval($id_perfil) : (isset($_SESSION['id_perfil']) ? intval($_SESSION['id_perfil']) : 2);
$nombre    = $nombre ?? $_SESSION['usuario'] ?? $_SESSION['mail'] ?? 'Usuario';
?>

<div class="content-wrapper">
    <div class="content-header pt-3 pb-2">
        <div class="container-fluid">
            <h1 class="m-0 text-dark h4 font-weight-bold">
                Dashboard <?= ($id_perfil === 1) ? 'Administrador' : 'de Usuario' ?>
            </h1>
        </div>
    </div>

    <!-- Contenido Principal -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header bg-primary">
                    <h3 class="card-title font-weight-bold h6 m-0">
                        <?php if ($id_perfil === 1): ?>
                            <i class="fas fa-chart-pie mr-1"></i> Gráficas Tickets Administrador
                        <?php else: ?>
                            <i class="fas fa-chart-bar mr-1"></i> Resumen Mis Solicitudes
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Gráfica de Pastel (Pie) -->
                        <div class="col-md-6 border-right">
                            <h6 class="text-center font-weight-bold text-secondary mb-3">
                                Estados Tickets Proyectos (Porcentaje)
                            </h6>
                            <div class="chart-container" style="position: relative; height: 260px; width: 100%;">
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>

                        <!-- Gráfica de Barras (Bar) -->
                        <div class="col-md-6">
                            <h6 class="text-center font-weight-bold text-secondary mb-3">
                                Estados Tickets Proyectos (Cantidad)
                            </h6>
                            <div class="chart-container" style="position: relative; height: 260px; width: 100%;">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Carga de Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const datosPHP = <?= json_encode($datosGraficas ?? []) ?>;

        const labels = datosPHP.map(item => item.categoria);
        const valores = datosPHP.map(item => item.total);

        const colores = ['#007bff', '#fd7e14', '#28a745', '#6f42c1', '#e83e8c', '#17a2b8'];

        // 1. Gráfica de Pastel (Pie)
        if (document.getElementById('pieChart')) {
            new Chart(document.getElementById('pieChart'), {
                type: 'pie',
                data: {
                    labels: labels.length ? labels : ['SOFTWARE', 'INFRAESTRUCTURA', 'REQUERIMIENTO'],
                    datasets: [{
                        data: valores.length ? valores : [10, 25, 15],
                        backgroundColor: colores
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Gráfica de Barras (Bar)
        if (document.getElementById('barChart')) {
            new Chart(document.getElementById('barChart'), {
                type: 'bar',
                data: {
                    labels: labels.length ? labels : ['SOFTWARE', 'INFRAESTRUCTURA', 'REQUERIMIENTO'],
                    datasets: [{
                        label: 'Cantidad Tickets',
                        data: valores.length ? valores : [10, 25, 15],
                        backgroundColor: '#fd7e14'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
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
    });
</script>