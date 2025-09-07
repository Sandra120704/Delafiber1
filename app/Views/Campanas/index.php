<?= $header ?>

<div class="container mt-4">

  <!-- Cabecera -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Campañas</h3>
    <a href="<?= site_url('campana/crear') ?>" class="btn btn-primary">+ Crear Campaña</a>
  </div>

    <!-- Tarjetas de resumen -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-white bg-primary">
        <div class="card-body">
          <h5 class="card-title">Total Campañas</h5>
          <p class="card-text"><?= count($campanas) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-success">
        <div class="card-body">
          <h5 class="card-title">Campañas Activas</h5>
          <p class="card-text"><?= $campanas_activas ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning">
        <div class="card-body">
          <h5 class="card-title">Presupuesto Total</h5>
          <p class="card-text">S/ <?= number_format($presupuesto_total, 2) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-info">
        <div class="card-body">
          <h5 class="card-title">Leads Generados</h5>
          <p class="card-text"><?= $total_leads ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabla de campañas -->
  <div class="card mb-4">
    <div class="card-body">
      <table id="campanasTable" class="table table-striped table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Fechas</th>
            <th>Presupuesto</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($campanas as $c): ?>
          <tr>
            <td><?= $c['idcampania'] ?></td>
            <td><?= $c['nombre'] ?></td>
            <td><?= $c['fecha_inicio'] ?> - <?= $c['fecha_fin'] ?></td>
            <td>S/ <?= number_format($c['presupuesto'],2) ?></td>
            <td>
              <span class="badge <?= $c['estado'] == 'Activo' ? 'bg-success' : 'bg-secondary' ?>">
                <?= $c['estado'] ?>
              </span>
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

  <div class="modal fade" id="detalleCampanaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle de Campaña</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
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
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script><script src="<?= base_url('js/campana.js') ?>"></script>
<script src="<?= base_url('js/campana.js') ?>"></script>
