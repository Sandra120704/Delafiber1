<?= $header ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<div class="content-wrapper">
    <div class="row">
        <!-- Tarjetas de KPIs -->
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-primary card-img-holder text-white">
                <div class="card-body">
                    <img src="<?= base_url('images/circle.svg') ?>" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Total Leads <i class="bx bx-trending-up mdi-24px float-right"></i></h4>
                    <h2 class="mb-5"><?= $total_leads ?? 0 ?></h2>
                    <h6 class="card-text">Leads registrados</h6>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-success card-img-holder text-white">
                <div class="card-body">
                    <img src="<?= base_url('images/circle.svg') ?>" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Conversiones <i class="bx bx-check-circle mdi-24px float-right"></i></h4>
                    <h2 class="mb-5"><?= $leads_convertidos ?? 0 ?></h2>
                    <h6 class="card-text">Este mes</h6>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-warning card-img-holder text-white">
                <div class="card-body">
                    <img src="<?= base_url('images/circle.svg') ?>" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Campañas Activas <i class="bx bx-bullseye mdi-24px float-right"></i></h4>
                    <h2 class="mb-5"><?= $campanias_activas ?? 0 ?></h2>
                    <h6 class="card-text">En ejecución</h6>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-info card-img-holder text-white">
                <div class="card-body">
                    <img src="<?= base_url('images/circle.svg') ?>" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Tareas Pendientes <i class="bx bx-list-check mdi-24px float-right"></i></h4>
                    <h2 class="mb-5"><?= $tareas_pendientes ?? 0 ?></h2>
                    <h6 class="card-text">Por completar</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Pipeline -->
        <div class="col-md-8 stretch-card grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pipeline de Ventas</h4>
                    <p class="card-description">Distribución de leads por etapa</p>
                    <canvas id="pipelineChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Actividad Reciente -->
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Actividad Reciente</h4>
                    <div class="list-wrapper">
                        <ul class="todo-list todo-list-rounded">
                            <?php if (!empty($actividad_reciente)): ?>
                                <?php foreach ($actividad_reciente as $actividad): ?>
                                <li class="d-block">
                                    <div class="form-check w-100">
                                        <label class="form-check-label">
                                            <strong><?= esc($actividad['nombres'] . ' ' . $actividad['apellidos']) ?></strong><br>
                                            <small class="text-muted"><?= esc($actividad['etapa_nombre']) ?></small><br>
                                            <small class="text-primary"><?= date('d/m/Y H:i', strtotime($actividad['fecha_registro'])) ?></small>
                                        </label>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="d-block text-center text-muted">No hay actividad reciente</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Campañas -->
        <div class="col-md-6 stretch-card grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Efectividad de Campañas</h4>
                    <p class="card-description">Leads generados por campaña</p>
                    <canvas id="campanasChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Leads por Usuario -->
        <div class="col-md-6 stretch-card grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Rendimiento por Usuario</h4>
                    <p class="card-description">Leads asignados por vendedor</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Leads</th>
                                    <th>Convertidos</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($rendimiento_usuarios)): ?>
                                    <?php foreach ($rendimiento_usuarios as $usuario): ?>
                                    <tr>
                                        <td><?= esc($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></td>
                                        <td><?= $usuario['total_leads'] ?></td>
                                        <td><?= $usuario['leads_convertidos'] ?></td>
                                        <td>
                                            <?php 
                                            $porcentaje = $usuario['total_leads'] > 0 ? 
                                                round(($usuario['leads_convertidos'] / $usuario['total_leads']) * 100, 1) : 0;
                                            ?>
                                            <span class="badge <?= $porcentaje >= 50 ? 'badge-success' : ($porcentaje >= 25 ? 'badge-warning' : 'badge-danger') ?>">
                                                <?= $porcentaje ?>%
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted">No hay datos disponibles</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos del Pipeline
    const pipelineData = <?= json_encode($pipeline_data ?? []) ?>;
    
    // Gráfico de Pipeline
    const pipelineCtx = document.getElementById('pipelineChart').getContext('2d');
    new Chart(pipelineCtx, {
        type: 'doughnut',
        data: {
            labels: pipelineData.map(item => item.nombre),
            datasets: [{
                data: pipelineData.map(item => item.total),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Datos de Campañas
    const campanasData = <?= json_encode($campanas_data ?? []) ?>;
    
    // Gráfico de Campañas
    if (campanasData.length > 0) {
        const campanasCtx = document.getElementById('campanasChart').getContext('2d');
        new Chart(campanasCtx, {
            type: 'bar',
            data: {
                labels: campanasData.map(item => item.nombre),
                datasets: [{
                    label: 'Leads Generados',
                    data: campanasData.map(item => item.total_leads),
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>

<?= $footer ?>