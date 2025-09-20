<?= $header ?>

<link rel="stylesheet" href="<?= base_url('css/tareas.css') ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<div class="content-wrapper">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Gestión de Tareas</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard/index') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tareas</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="mostrarCalendario()">
                <i class="bx bx-calendar"></i> Calendario
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTarea">
                <i class="bx bx-plus"></i> Nueva Tarea
            </button>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4><?= $estadisticas['total'] ?></h4>
                    <small>Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4><?= $estadisticas['pendientes'] ?></h4>
                    <small>Pendientes</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4><?= $estadisticas['proceso'] ?></h4>
                    <small>En Progreso</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4><?= $estadisticas['completadas'] ?></h4>
                    <small>Completadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4><?= $estadisticas['vencidas'] ?? 0 ?></h4>
                    <small>Vencidas</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h4><?= $estadisticas['mis_tareas'] ?></h4>
                    <small>Mis Tareas</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('tareas') ?>?filtro=todas" 
                           class="btn <?= $filtro_actual == 'todas' ? 'btn-primary' : 'btn-outline-primary' ?>">
                            Todas
                        </a>
                        <a href="<?= base_url('tareas') ?>?filtro=pendientes" 
                           class="btn <?= $filtro_actual == 'pendientes' ? 'btn-warning' : 'btn-outline-warning' ?>">
                            Pendientes
                        </a>
                        <a href="<?= base_url('tareas') ?>?filtro=proceso" 
                           class="btn <?= $filtro_actual == 'proceso' ? 'btn-info' : 'btn-outline-info' ?>">
                            En Progreso
                        </a>
                        <a href="<?= base_url('tareas') ?>?filtro=completadas" 
                           class="btn <?= $filtro_actual == 'completadas' ? 'btn-success' : 'btn-outline-success' ?>">
                            Completadas
                        </a>
                        <a href="<?= base_url('tareas') ?>?filtro=mis_tareas" 
                           class="btn <?= $filtro_actual == 'mis_tareas' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                            Mis Tareas
                        </a>
                        <a href="<?= base_url('tareas') ?>?filtro=hoy" 
                           class="btn <?= $filtro_actual == 'hoy' ? 'btn-dark' : 'btn-outline-dark' ?>">
                            Hoy
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar tareas..." id="buscarTarea">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Tareas -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Lista de Tareas</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($tareas)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Estado</th>
                                <th>Descripción</th>
                                <th>Lead</th>
                                <th>Asignado a</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tareas as $tarea): ?>
                            <tr class="<?= $tarea['estado'] == 'Completada' ? 'table-success' : ($tarea['fecha_fin'] < date('Y-m-d') && $tarea['estado'] != 'Completada' ? 'table-danger' : '') ?>">
                                <td>
                                    <select class="form-select form-select-sm estado-select" data-id="<?= $tarea['idtarea'] ?>">
                                        <option value="Pendiente" <?= $tarea['estado'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                        <option value="En progreso" <?= $tarea['estado'] == 'En progreso' ? 'selected' : '' ?>>En progreso</option>
                                        <option value="Completada" <?= $tarea['estado'] == 'Completada' ? 'selected' : '' ?>>Completada</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= esc(substr($tarea['descripcion'], 0, 50)) ?>...</div>
                                    <?php if ($tarea['fecha_fin'] < date('Y-m-d') && $tarea['estado'] != 'Completada'): ?>
                                        <small class="text-danger"><i class="bx bx-time"></i> Vencida</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= esc($tarea['lead_nombres'] . ' ' . $tarea['lead_apellidos']) ?></div>
                                    <small class="text-muted"><?= esc($tarea['telefono']) ?></small>
                                </td>
                                <td><?= esc($tarea['asignado_a']) ?></td>
                                <td><?= date('d/m/Y', strtotime($tarea['fecha_inicio'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($tarea['fecha_fin'])) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btn-editar" data-id="<?= $tarea['idtarea'] ?>">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info btn-ver" data-id="<?= $tarea['idtarea'] ?>">
                                            <i class="bx bx-show"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-eliminar" data-id="<?= $tarea['idtarea'] ?>">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bx bx-task display-1 text-muted"></i>
                    <h5 class="text-muted">No hay tareas para mostrar</h5>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalTarea">
                        <i class="bx bx-plus"></i> Crear primera tarea
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para crear/editar tarea -->
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formTarea">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Tarea</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idtarea" name="idtarea">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Lead</label>
                                <select class="form-select" name="idlead" required>
                                    <option value="">Seleccionar lead</option>
                                    <?php if (!empty($leads)): ?>
                                        <?php foreach ($leads as $lead): ?>
                                            <option value="<?= $lead['idlead'] ?>">
                                                <?= esc($lead['nombres'] . ' ' . $lead['apellidos']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Asignar a</label>
                                <select class="form-select" name="idusuario" required>
                                    <option value="">Seleccionar usuario</option>
                                    <?php if (!empty($usuarios)): ?>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <option value="<?= $usuario['idusuario'] ?>">
                                                <?= esc($usuario['usuario']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3" required 
                                  placeholder="Describe la tarea a realizar..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" name="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" name="fecha_fin" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado" required>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="En progreso">En progreso</option>
                                    <option value="Completada">Completada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de tarea -->
<div class="modal fade" id="modalDetalleTarea" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detallesTareaContent">
                <!-- Contenido se carga vía AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
    const base_url = "<?= rtrim(base_url(), '/') ?>";
</script>

<script type="module" src="<?= base_url('js/tareaJS/tarea.js') ?>"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<?= $footer ?>