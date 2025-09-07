<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/campanas.css') ?>">
<div class="container mt-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3><?= isset($campana) ? 'Editar Campaña' : 'Crear Campaña' ?></h3>
    <a href="<?= site_url('campanas') ?>" class="btn btn-secondary">Volver al listado</a>
  </div>

  <form action="<?= base_url('campana/guardar') ?>" method="POST" id="form-campana">
    <?php if(isset($campana)) : ?>
      <input type="hidden" name="idcampania" value="<?= $campana['idcampania'] ?>">
    <?php endif; ?>

    <!-- Datos generales de la campaña -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="mb-3">
          <label>Nombre de la Campaña</label>
          <input type="text" name="nombre" class="form-control" required value="<?= $campana['nombre'] ?? '' ?>">
        </div>
        <div class="mb-3">
          <label>Descripción</label>
          <textarea name="descripcion" class="form-control"><?= $campana['descripcion'] ?? '' ?></textarea>
        </div>
        <div class="row g-2 mb-3">
          <div class="col-md-6">
            <label>Fecha Inicio</label>
            <input type="date" name="fecha_inicio" class="form-control" required value="<?= $campana['fecha_inicio'] ?? '' ?>">
          </div>
          <div class="col-md-6">
            <label>Fecha Fin</label>
            <input type="date" name="fecha_fin" class="form-control" required value="<?= $campana['fecha_fin'] ?? '' ?>">
          </div>
        </div>
        <div class="mb-3">
          <label>Presupuesto</label>
          <input type="number" step="0.01" name="presupuesto" class="form-control" required value="<?= $campana['presupuesto'] ?? '' ?>">
        </div>
        <div class="mb-3">
          <label>Estado</label>
          <select name="estado" class="form-control" required>
            <option value="Activo" <?= isset($campana) && $campana['estado']=='Activo'?'selected':'' ?>>Activo</option>
            <option value="Inactivo" <?= isset($campana) && $campana['estado']=='Inactivo'?'selected':'' ?>>Inactivo</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Medios Dinámicos -->
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span>Difusión en Medios</span>
        <div>
          <button type="button" class="btn btn-sm btn-primary" id="agregarMedioBtn">+ Agregar Medio</button>
          <button type="button" class="btn btn-sm btn-success" id="nuevoMedioBtn">+ Nuevo Medio</button>
        </div>
      </div>
      <div class="card-body">
        <div id="mediosContainer">
          <div class="row g-2 mb-2 medio-row">
            <div class="col-md-6">
              <select name="medios[]" class="form-control" required>
                <option value="">Seleccione Medio</option>
                <?php foreach($medios as $m): ?>
                  <option value="<?= $m['idmedio'] ?>"><?= $m['nombre'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <input type="number" step="0.01" name="inversion[]" class="form-control" placeholder="Inversión">
            </div>
            <div class="col-md-2">
              <button type="button" class="btn btn-danger btn-sm eliminarMedio">Eliminar</button>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="text-end mb-4">
      <button type="reset" class="btn btn-outline-secondary">Cancelar</button>
      <button type="submit" class="btn btn-primary">Guardar Campaña</button>
    </div>
  </form>
</div>
<!-- Modal Nuevo Medio -->
<div class="modal fade" id="modalNuevoMedio" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo Medio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="nombreMedio" class="form-label">Nombre del Medio</label>
          <input type="text" id="nombreMedio" class="form-control" placeholder="Nombre del medio">
        </div>
        <div class="mb-3">
          <label for="descMedio" class="form-label">Descripción</label>
          <textarea id="descMedio" class="form-control" placeholder="Descripción opcional"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="guardarMedioBtn">Guardar Medio</button>
      </div>
    </div>
  </div>
</div>


<?= $footer ?>


