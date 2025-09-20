<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/campanas.css') ?>"> 
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<div class="container-fluid mt-4 px-4">
    
    <!-- Notificaciones -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
        
    <!-- Header mejorado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Gestión de Campañas</h2>
            <p class="text-muted mb-0">Dashboard completo de marketing</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" id="exportBtn">
                <i class="bi bi-download"></i> Exportar
            </button>
            <div class="btn-group">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCampaignModal">
                    <i class="bi bi-plus-lg"></i> Nueva Campaña
                </button>
                <a href="<?= site_url('campanas/crear') ?>" class="btn btn-primary border-start border-white border-opacity-25" title="Formulario completo">
                    <i class="bi bi-plus-circle"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Métricas mejoradas con gráficos mini -->
    <div class="row mb-4 g-3">
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card text-white shadow-lg h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-bullseye fs-3 me-2"></i>
                            <h6 class="mb-0">Total Campañas</h6>
                        </div>
                        <h2 class="mb-0" id="cardTotalCampanas"><?= $metricas['total_campanas'] ?? 0 ?></h2>
                        <small class="opacity-75">Total registradas</small>
                    </div>
                    <canvas id="totalChart" width="50" height="30"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card success text-white shadow-lg h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-lightning-charge-fill fs-3 me-2"></i>
                            <h6 class="mb-0">Activas</h6>
                        </div>
                        <h2 class="mb-0" id="cardCampanasActivas"><?= $metricas['activas'] ?? 0 ?></h2>
                        <small class="opacity-75"><?= round(($metricas['activas'] ?? 0) / max(($metricas['total_campanas'] ?? 1), 1) * 100, 1) ?>% del total</small>
                    </div>
                    <canvas id="activeChart" width="50" height="30"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card warning text-white shadow-lg h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-cash-stack fs-3 me-2"></i>
                            <h6 class="mb-0">Presupuesto</h6>
                        </div>
                        <h2 class="mb-0" id="cardPresupuestoTotal">S/ <?= number_format($metricas['presupuesto_total'] ?? 0, 0) ?></h2>
                        <small class="opacity-75"><?= $metricas['porcentaje_gastado'] ?? 0 ?>% utilizado</small>
                    </div>
                    <div class="text-end">
                        <div class="progress progress-thin mb-2" style="width: 60px;">
                            <div class="progress-bar bg-white" style="width: <?= $metricas['porcentaje_gastado'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card info text-white shadow-lg h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-people-fill fs-3 me-2"></i>
                            <h6 class="mb-0">Total Leads</h6>
                        </div>
                        <h2 class="mb-0" id="cardTotalLeads"><?= $metricas['total_leads'] ?? 0 ?></h2>
                        <small class="opacity-75">ROI: <?= $metricas['roi_promedio'] ?? 0 ?>%</small>
                    </div>
                    <i class="bi bi-graph-up fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros avanzados -->
    <div class="filter-section mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control" id="searchFilter" placeholder="Nombre, descripción...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Estado</label>
                <select class="form-select" id="statusFilter">
                    <option value="">Todos</option>
                    <option value="Activa">Activa</option>
                    <option value="Inactiva">Inactiva</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Responsable</label>
                <select class="form-select" id="responsableFilter">
                    <option value="">Todos</option>
                    <?php foreach($usuarios as $usuario): ?>
                        <option value="<?= $usuario['idusuario'] ?>"><?= $usuario['nombre_completo'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Rango de fechas</label>
                <div class="input-group">
                    <input type="date" class="form-control" id="startDate">
                    <input type="date" class="form-control" id="endDate">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button class="btn btn-primary" id="applyFilters">
                        <i class="bi bi-funnel"></i> Aplicar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla mejorada -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lista de Campañas</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="campaignsTable" class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Campaña</th>
                            <th>Estado</th>
                            <th>Fechas</th>
                            <th>Presupuesto</th>
                            <th>ROI</th>
                            <th>Leads</th>
                            <th>Responsable</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($campanas as $campana): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= $campana['nombre'] ?></div>
                                <small class="text-muted"><?= substr($campana['descripcion'], 0, 50) ?>...</small>
                            </td>
                            <td>
                                <button class="btn btn-sm toggle-estado <?= $campana['estado'] == 'Activa' ? 'btn-success' : 'btn-secondary' ?>" 
                                        data-id="<?= $campana['idcampania'] ?>" 
                                        data-estado="<?= $campana['estado'] == 'Activa' ? 'Inactiva' : 'Activa' ?>">
                                    <?= $campana['estado'] ?>
                                </button>
                            </td>
                            <td>
                                <div><?= date('d/m/Y', strtotime($campana['fecha_inicio'])) ?></div>
                                <small class="text-muted">a <?= date('d/m/Y', strtotime($campana['fecha_fin'])) ?></small>
                            </td>
                            <td>S/ <?= number_format($campana['presupuesto'], 2) ?></td>
                            <td>
                                <?php 
                                $roi = $campana['inversion_total'] > 0 ? 
                                    round(($campana['leads_total'] / $campana['inversion_total']) * 100, 2) : 0;
                                ?>
                                <span class="badge <?= $roi > 50 ? 'bg-success' : ($roi > 20 ? 'bg-warning' : 'bg-danger') ?>">
                                    <?= $roi ?>%
                                </span>
                            </td>
                            <td><?= $campana['leads_total'] ?? 0 ?></td>
                            <td><?= $campana['responsable_nombre'] ?? 'No asignado' ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-detalle" 
                                            data-id="<?= $campana['idcampania'] ?>" 
                                            title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="<?= site_url('campanas/crear/'.$campana['idcampania']) ?>" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-outline-danger btn-eliminar" 
                                            data-id="<?= $campana['idcampania'] ?>" 
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Crear Campaña -->
<div class="modal fade" id="createCampaignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle"></i> Nueva Campaña
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createCampaignForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Presupuesto *</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" name="presupuesto" class="form-control" min="0" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Responsable</label>
                            <select name="responsable" class="form-select">
                                <option value="">Seleccionar responsable</option>
                                <?php foreach($usuarios as $usuario): ?>
                                    <option value="<?= $usuario['idusuario'] ?>" 
                                            <?= $usuario['idusuario'] == session()->get('idusuario') ? 'selected' : '' ?>>
                                        <?= $usuario['nombre_completo'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Estado oculto, por defecto Activa -->
                        <input type="hidden" name="estado" value="Activa">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="createCampaignForm" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Crear Campaña
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle de Campaña -->
<div class="modal fade" id="detalleCampanaModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-eye"></i> Detalle de Campaña
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h6>Información General</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Nombre:</strong></td><td id="detalleNombre">-</td></tr>
                            <tr><td><strong>Descripción:</strong></td><td id="detalleDescripcion">-</td></tr>
                            <tr><td><strong>Fechas:</strong></td><td id="detalleFechas">-</td></tr>
                            <tr><td><strong>Presupuesto:</strong></td><td>S/ <span id="detallePresupuesto">0</span></td></tr>
                            <tr><td><strong>Estado:</strong></td><td id="detalleEstado">-</td></tr>
                            <tr><td><strong>Responsable:</strong></td><td id="detalleResponsable">-</td></tr>
                        </table>
                    </div>
                    <div class="col-lg-6">
                        <h6>Medios de Difusión</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Medio</th>
                                    <th>Inversión</th>
                                    <th>Leads</th>
                                </tr>
                            </thead>
                            <tbody id="detalleMedios">
                                <tr><td colspan="3">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

<script>
    const BASE_URL = '<?= base_url() ?>';
</script>

<script src="<?= base_url('js/campanasJS/modal-validation.js') ?>"></script>
<script src="<?= base_url('js/campanasJS/campana.js') ?>"></script>

<?= $footer ?>