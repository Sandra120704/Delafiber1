<?= $header ?>

<div class="main-card">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-0">
        <?= isset($persona) ? 'Editar Persona' : 'Nueva Persona' ?>
      </h3>
      <small class="text-muted">
        <?= isset($persona) ? 'Actualiza los datos del contacto' : 'Completa el formulario para registrar una persona' ?>
      </small>
    </div>
    <a href="<?= site_url('personas') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  <!-- Formulario -->
  <form action="<?= site_url('personas/guardar') ?>" method="post">
    <?php if (isset($persona)): ?>
      <input type="hidden" name="idpersona" value="<?= $persona['idpersona'] ?>">
    <?php endif; ?>

    <div class="mb-3">
      <label class="form-label">DNI</label>
      <input type="text" name="dni" value="<?= esc($persona['dni'] ?? '') ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Nombres</label>
      <input type="text" name="nombres" value="<?= esc($persona['nombres'] ?? '') ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Apellidos</label>
      <input type="text" name="apellidos" value="<?= esc($persona['apellidos'] ?? '') ?>" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Correo</label>
      <input type="email" name="correo" value="<?= esc($persona['correo'] ?? '') ?>" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">Teléfono</label>
      <input type="text" name="telefono" value="<?= esc($persona['telefono'] ?? '') ?>" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">Dirección</label>
      <input type="text" name="direccion" value="<?= esc($persona['direccion'] ?? '') ?>" class="form-control">
    </div>

    <!-- Select Distrito -->
    <div class="mb-3">
      <label for="iddistrito" class="form-label">Distrito</label>
      <select class="form-control" name="iddistrito" id="iddistrito" required>
        <option value="">Seleccione...</option>
        <?php foreach ($distritos as $d): ?>
          <option value="<?= $d['iddistrito'] ?>"
            <?= (isset($persona) && $persona['iddistrito'] == $d['iddistrito']) ? 'selected' : '' ?>>
            <?= $d['nombre'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="text-end">
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-save"></i>
        <?= isset($persona) ? 'Guardar Cambios' : 'Registrar Persona' ?>
      </button>
    </div>
  </form>
</div>

<?= $footer ?>
