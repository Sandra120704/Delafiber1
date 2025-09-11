<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/campanas.css') ?>"> 
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<div class="container mt-4">

  <!-- Header y botón Crear -->
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <h3>Campañas</h3>
    <a href="<?= site_url('campana/crear') ?>" class="btn btn-primary mt-2 mt-md-0">+ Crear Campaña</a>
  </div>

  <!-- Cards de resumen -->
  <div class="row mb-4 g-3">
    <div class="col-sm-6 col-md-3">
      <div class="card text-white bg-primary shadow-lg rounded-4 text-center">
        <div class="card-body">
          <i class="bi bi-graph-up-arrow fs-1 mb-2"></i>
          <h5 class="card-title">Total Campañas</h5>
          <p class="card-text display-6 fw-bold" id="cardTotalCampanas"><?= count($campanas) ?></p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card text-white shadow-lg rounded-4 text-center" style="background-color: #60219bff;">
        <div class="card-body">
          <i class="bi bi-lightning-charge-fill fs-1 mb-2"></i>
          <h5 class="card-title">Campañas Activas</h5>
          <p class="card-text display-6 fw-bold" id="cardCampanasActivas"><?= $campanas_activas ?></p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card text-white bg-warning shadow-lg rounded-4 text-center">
        <div class="card-body">
          <i class="bi bi-cash-coin fs-1 mb-2"></i>
          <h5 class="card-title">Presupuesto Total</h5>
          <p class="card-text display-6 fw-bold" id="cardPresupuestoTotal">S/ <?= number_format($presupuesto_total, 2) ?></p>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card text-white bg-info shadow-lg rounded-4 text-center">
        <div class="card-body">
          <i class="bi bi-people-fill fs-1 mb-2"></i>
          <h5 class="card-title">Leads Generados</h5>
          <p class="card-text display-6 fw-bold" id="cardTotalLeads"><?= $total_leads ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabla de Campañas -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table id="campanasTable" class="table table-striped table-hover nowrap" style="width:100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Fechas</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($campanas as $c): ?>
            <tr>
              <td><?= $c['idcampania'] ?></td>
              <td><?= $c['nombre'] ?></td>
              <td>
                <span title="<?= $c['descripcion'] ?>">
                  <?= strlen($c['descripcion']) > 50 ? substr($c['descripcion'], 0, 50).'...' : $c['descripcion'] ?>
                </span>
              </td>
              <td><?= $c['fecha_inicio'] ?> - <?= $c['fecha_fin'] ?></td>
              <td>
                <?php if ($c['estado'] == 'Activo'): ?>
                  <button class="btn btn-sm btn-success toggle-estado" data-id="<?= $c['idcampania'] ?>" data-estado="Inactivo">Activo</button>
                <?php else: ?>
                  <button class="btn btn-sm btn-secondary toggle-estado" data-id="<?= $c['idcampania'] ?>" data-estado="Activo">Inactivo</button>
                <?php endif; ?>
              </td>
              <td>
                <a href="javascript:void(0)" class="btn btn-sm btn-info btn-detalle" data-id="<?= $c['idcampania'] ?>">Detalle</a>
                <a href="<?= site_url('campana/form/'.$c['idcampania']) ?>" class="btn btn-sm btn-outline-warning">Editar</a>
                <a href="<?= site_url('campana/eliminar/'.$c['idcampania']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar campaña?')">Eliminar</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Detalle -->
  <div class="modal fade" id="detalleCampanaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle de Campaña</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <strong>Nombre:</strong> <span id="detalleNombre"></span><br>
            <strong>Descripción:</strong> <span id="detalleDescripcion"></span><br>
            <strong>Fechas:</strong> <span id="detalleFechas"></span><br>
            <strong>Presupuesto:</strong> S/ <span id="detallePresupuesto"></span><br>
            <strong>Estado:</strong> <span id="detalleEstado"></span><br>
            <strong>Responsable:</strong> <span id="detalleResponsable"><?= $campana['responsable_nombre'] ?? 'No asignado' ?></span><br>
            <strong>Fecha de creación:</strong> <span id="detalleFechaCreacion"></span><br>
            <strong>Segmentación:</strong> <span id="detalleSegmento"></span><br>
            <strong>Objetivos / Métricas:</strong> <span id="detalleObjetivos"></span><br>
            <strong>Notas internas:</strong> <span id="detalleNotas"></span>
          </div>
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Medio</th>
                <th>Inversión</th>
                <th>Leads Generados</th>
              </tr>
            </thead>
            <tbody id="detalleMedios">
              <tr><td colspan="3">Seleccione una campaña para ver los detalles</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script>
const BASE_URL = "<?= base_url() ?>";
</script>
<script src="<?= base_url('js/CampanasJS/campana.js') ?>"></script>
<?= $footer ?>
