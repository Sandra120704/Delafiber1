<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form action="<?= site_url('personas/guardar') ?>" method="POST" id="form-editar-persona" autocomplete="off">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Editar Persona</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="idpersona" value="<?= esc($persona['idpersona'] ?? '') ?>">

          <div class="mb-3">
            <label for="dni" class="form-label">DNI</label>
            <input type="text" class="form-control" name="dni" id="dni" maxlength="8" minlength="8" required autofocus value="<?= esc($persona['dni'] ?? '') ?>">
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label for="apellidos" class="form-label">Apellidos</label>
              <input type="text" class="form-control" name="apellidos" id="apellidos" required value="<?= esc($persona['apellidos'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label for="nombres" class="form-label">Nombres</label>
              <input type="text" class="form-control" name="nombres" id="nombres" required value="<?= esc($persona['nombres'] ?? '') ?>">
            </div>
          </div>

          <div class="row g-3 mt-3">
            <div class="col-md-8">
              <label for="correo" class="form-label">Correo Electrónico</label>
              <input type="email" class="form-control" name="correo" id="correo" value="<?= esc($persona['correo'] ?? '') ?>">
            </div>
            <div class="col-md-4">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="text" class="form-control" name="telefono" id="telefono" maxlength="9" pattern="[0-9]*" title="Solo se permiten números" required value="<?= esc($persona['telefono'] ?? '') ?>">
            </div>
          </div>

          <div class="row g-3 mt-3">
            <div class="col-md-8">
              <label for="direccion" class="form-label">Dirección</label>
              <input type="text" class="form-control" name="direccion" id="direccion" value="<?= esc($persona['direccion'] ?? '') ?>">
            </div>
            <div class="col-md-4">
              <label for="iddistrito" class="form-label">Distrito</label>
              <select class="form-control" name="iddistrito" id="iddistrito" required>
                <option value="">Seleccione...</option>
                <?php foreach ($distritos as $d): ?>
                  <option value="<?= $d['iddistrito'] ?>" <?= (isset($persona) && $persona['iddistrito'] == $d['iddistrito']) ? 'selected' : '' ?>>
                    <?= esc($d['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="mt-3">
            <label for="referencias" class="form-label">Referencia</label>
            <input type="text" class="form-control" name="referencias" id="referencias" value="<?= esc($persona['referencias'] ?? '') ?>">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form-editar-persona');
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // Éxito
        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
        modal.hide();

        Swal.fire({
          icon: 'success',
          title: 'Actualizado',
          text: 'Los datos fueron guardados correctamente',
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          location.reload(); // O actualizas tabla por AJAX
        });

      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.message || 'No se pudo guardar los cambios.'
        });
      }
    })
    .catch(err => {
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'Error del servidor',
        text: 'Intenta nuevamente más tarde.'
      });
    });
  });
});


</script>
