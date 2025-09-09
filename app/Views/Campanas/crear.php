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
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Agregar Medio - clona un nuevo medio-row dentro de mediosContainer
  const agregarMedioBtn = document.getElementById('agregarMedioBtn');
  const mediosContainer = document.getElementById('mediosContainer');

  agregarMedioBtn.addEventListener('click', function() {
    const nuevoMedioRow = document.querySelector('.medio-row').cloneNode(true);
    // Limpiar valores del nuevo row
    nuevoMedioRow.querySelector('select').value = '';
    nuevoMedioRow.querySelector('input').value = '';
    mediosContainer.appendChild(nuevoMedioRow);
    agregarEliminarEvent(nuevoMedioRow);
  });

  // Función para añadir evento eliminar a los botones eliminar
  function agregarEliminarEvent(medioRow) {
    const btnEliminar = medioRow.querySelector('.eliminarMedio');
    btnEliminar.addEventListener('click', function() {
      if(document.querySelectorAll('.medio-row').length > 1) {
        medioRow.remove();
      } else {
        alert('Debe haber al menos un medio');
      }
    });
  }

  // Añadir evento eliminar al medio original
  document.querySelectorAll('.medio-row').forEach(row => {
    agregarEliminarEvent(row);
  });

  // Abrir modal para Nuevo Medio
  const nuevoMedioBtn = document.getElementById('nuevoMedioBtn');
  const modalNuevoMedio = new bootstrap.Modal(document.getElementById('modalNuevoMedio'));

  nuevoMedioBtn.addEventListener('click', function() {
    modalNuevoMedio.show();
  });

  // Guardar Nuevo Medio (debes implementar la lógica AJAX o formulario para guardar realmente)
  const guardarMedioBtn = document.getElementById('guardarMedioBtn');
  guardarMedioBtn.addEventListener('click', function() {
    const nombre = document.getElementById('nombreMedio').value.trim();
    const descripcion = document.getElementById('descMedio').value.trim();

    if(nombre === '') {
      alert('El nombre del medio es obligatorio');
      return;
    }

    // Aquí deberías hacer un AJAX para guardar el medio en la base de datos
    // Por ahora solo cierro el modal y agrego el medio nuevo a los select

    // Agregar nuevo medio a todos los selects de medios
    const nuevosSelects = document.querySelectorAll('select[name="medios[]"]');
    nuevosSelects.forEach(select => {
      const option = document.createElement('option');
      option.value = 'nuevo'; // Cambiar al ID real que devuelva la DB
      option.textContent = nombre;
      select.appendChild(option);
    });

    // Limpiar y cerrar modal
    document.getElementById('nombreMedio').value = '';
    document.getElementById('descMedio').value = '';
    modalNuevoMedio.hide();

    alert('Medio agregado (simulado). Implementa el guardado en backend.');
  });
});
</script>



