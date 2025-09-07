<?= $header ?>

<div class="container mt-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3><?= isset($campana) ? 'Editar Campaña' : 'Crear Campaña' ?></h3>
    <a href="<?= site_url('campanas') ?>" class="btn btn-secondary">Volver al listado</a>
  </div>

  <form action="<?= base_url('campana/guardar') ?>" method="POST" id="form-campana">
    <?php if(isset($campana)) : ?>
      <input type="hidden" name="idcampania" value="<?= $campana['idcampania'] ?>">
    <?php endif; ?>

    <!-- Datos Generales -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre de la Campaña</label>
          <input type="text" class="form-control" name="nombre" id="nombre" required
            value="<?= $campana['nombre'] ?? '' ?>">
        </div>

        <div class="mb-3">
          <label for="descripcion" class="form-label">Descripción</label>
          <textarea class="form-control" name="descripcion" id="descripcion"><?= $campana['descripcion'] ?? '' ?></textarea>
        </div>

        <div class="row g-2 mb-3">
          <div class="col-md-6">
            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
            <input type="date" class="form-control" name="fecha_inicio" required
              value="<?= $campana['fecha_inicio'] ?? '' ?>">
          </div>
          <div class="col-md-6">
            <label for="fecha_fin" class="form-label">Fecha Fin</label>
            <input type="date" class="form-control" name="fecha_fin" required
              value="<?= $campana['fecha_fin'] ?? '' ?>">
          </div>
        </div>

        <div class="mb-3">
          <label for="presupuesto" class="form-label">Presupuesto</label>
          <input type="number" step="0.01" class="form-control" name="presupuesto" required
            value="<?= $campana['presupuesto'] ?? '' ?>">
        </div>

        <div class="mb-3">
          <label for="estado" class="form-label">Estado</label>
          <select name="estado" class="form-select" required>
            <option value="Activo" <?= isset($campana) && $campana['estado'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
            <option value="Inactivo" <?= isset($campana) && $campana['estado'] == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Difusión en Medios -->
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span>Difusión en Medios</span>
        <button type="button" class="btn btn-sm btn-primary" id="agregarMedio">+ Agregar Medio</button>
      </div>
      <div class="card-body" id="mediosContainer">
        <?php if(!empty($difusiones)) : ?>
          <?php foreach($difusiones as $d) : ?>
            <div class="row g-2 mb-2 medio-row">
              <div class="col-md-4">
                <select name="medios[][idmedio]" class="form-select" required>
                  <option value="">Seleccione Medio</option>
                  <?php foreach($medios as $m): ?>
                    <option value="<?= $m['idmedio'] ?>" <?= $d['idmedio']==$m['idmedio'] ? 'selected' : '' ?>>
                      <?= $m['nombre'] ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-3">
                <input type="number" step="0.01" name="medios[][inversion]" class="form-control"
                  placeholder="Inversión" value="<?= $d['inversion'] ?? 0 ?>" required>
              </div>
              <div class="col-md-3">
                <input type="number" name="medios[][leads_generados]" class="form-control"
                  placeholder="Leads Generados" value="<?= $d['leads_generados'] ?? 0 ?>" required>
              </div>
              <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm eliminarMedio">Eliminar</button>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Fila inicial -->
          <div class="row g-2 mb-2 medio-row">
            <div class="col-md-4">
              <select name="medios[][idmedio]" class="form-select" required>
                <option value="">Seleccione Medio</option>
                <?php foreach($medios as $m): ?>
                  <option value="<?= $m['idmedio'] ?>"><?= $m['nombre'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <input type="number" step="0.01" name="medios[][inversion]" class="form-control"
                placeholder="Inversión" value="0" required>
            </div>
            <div class="col-md-3">
              <input type="number" name="medios[][leads_generados]" class="form-control"
                placeholder="Leads Generados" value="0" required>
            </div>
            <div class="col-md-2">
              <button type="button" class="btn btn-danger btn-sm eliminarMedio">Eliminar</button>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="text-end mb-4">
      <button type="reset" class="btn btn-outline-secondary">Cancelar</button>
      <button type="submit" class="btn btn-primary">Guardar Campaña</button>
    </div>

  </form>
</div>

<?= $footer ?>

<script>
document.addEventListener('DOMContentLoaded', () => {

  const mediosContainer = document.getElementById('mediosContainer');
  const btnAgregar = document.getElementById('agregarMedio');

  btnAgregar.addEventListener('click', () => {
    const nuevaFila = document.querySelector('.medio-row').cloneNode(true);
    nuevaFila.querySelectorAll('input').forEach(i => i.value = 0);
    nuevaFila.querySelector('select').value = '';
    mediosContainer.appendChild(nuevaFila);
    attachEliminar();
  });

  function attachEliminar() {
    document.querySelectorAll('.eliminarMedio').forEach(btn => {
      btn.onclick = () => btn.closest('.medio-row').remove();
    });
  }

  attachEliminar();
});
</script>
