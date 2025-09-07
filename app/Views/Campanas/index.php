<?= $header ?>
<style>
.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.8rem 1.2rem rgba(0,0,0,0.25);
}
.card .card-body i {
  font-size: 1.0rem;
  margin-bottom: 10px;
  display: block;
}
.card .card-title, 
.card .card-text {
  color: white !important; /* asegura blanco */
}
</style>
<div class="container mt-4">

  <!-- Cabecera -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Campañas</h3>
    <a href="<?= site_url('campana/crear') ?>" class="btn btn-primary">+ Crear Campaña</a>
  </div>

  <!-- Tarjetas de resumen -->
  <div class="row mb-4" id="c1">
    <div class="col-md-3">
    <div class="card text-white bg-primary shadow-lg rounded-4 text-white">
      <div class="card-body text-center">
        <i class="bi bi-graph-up-arrow fs-1 mb-2"></i>
        <h5 class="card-title">Total Campañas</h5>
        <p class="card-text display-6 fw-bold"><?= count($campanas) ?></p>
      </div>
    </div>
  </div>

  <!-- Campañas Activas -->
  <div class="col-md-3">
    <div class="card text-white shadow-lg rounded-4" style="background-color: #82219bff; color:white;">
      <div class="card-body text-center">
        <i class="bi bi-lightning-charge-fill fs-1 mb-2"></i>
        <h5 class="card-title">Campañas Activas</h5>
        <p class="card-text display-6 fw-bold" id="cardCampanasActivas"><?= $campanas_activas ?></p>
      </div>
    </div>
  </div>

  <!-- Presupuesto Total -->
  <div class="col-md-3">
    <div class="card text-white bg-warning shadow-lg rounded-4">
      <div class="card-body text-center">
        <i class="bi bi-cash-coin fs-1 mb-2"></i>
        <h5 class="card-title">Presupuesto Total</h5>
        <p class="card-text display-6 fw-bold">S/ <?= number_format($presupuesto_total, 2) ?></p>
      </div>
    </div>
  </div>

  <!-- Leads Generados -->
  <div class="col-md-3">
    <div class="card text-white bg-info shadow-lg rounded-4">
      <div class="card-body text-center">
        <i class="bi bi-people-fill fs-1 mb-2"></i>
        <h5 class="card-title">Leads Generados</h5>
        <p class="card-text display-6 fw-bold"><?= $total_leads ?></p>
      </div>
    </div>
  </div>
</div>
  </div>

  <!-- Tabla de campañas -->
  <div class="card mb-4">
    <div class="card-body">
      <div class="table-responsive">
      <table id="campanasTable" class="table table-striped table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Fechas</th>
           <!--  <th>Presupuesto</th> -->
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
          <!--   <td>S/ <?= number_format($c['presupuesto'],2) ?></td> -->
            <td>
            <?php if ($c['estado'] == 'Activo'): ?>
              <button 
                class="btn btn-sm btn-success toggle-estado" 
                data-id="<?= $c['idcampania'] ?>" 
                data-estado="Inactivo">
                Activo
              </button>
            <?php else: ?>
              <button 
                class="btn btn-sm btn-secondary toggle-estado" 
                data-id="<?= $c['idcampania'] ?>" 
                data-estado="Activo">
                Inactivo
              </button>
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
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle de Campaña</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <!-- Info General -->
          <div class="mb-3">
            <strong>Nombre:</strong> <span id="detalleNombre"></span><br>
            <strong>Descripción:</strong> <span id="detalleDescripcion"></span><br>
            <strong>Fechas:</strong> <span id="detalleFechas"></span><br>
            <strong>Presupuesto:</strong> S/ <span id="detallePresupuesto"></span><br>
            <strong>Estado:</strong> <span id="detalleEstado"></span>
          </div>

          <!-- Medios -->
          <table class="table table-striped">
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

<?= $footer ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script>
  const BASE_URL = "<?= base_url() ?>";
</script>
<script src="<?= base_url('js/campana.js') ?>"></script>
