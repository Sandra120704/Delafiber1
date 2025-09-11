<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="leadForm">
        <div class="modal-header">
          <h5 class="modal-title">Convertir a Lead</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <!-- Datos de la persona -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">DNI</label>
              <input type="text" class="form-control" value="<?= esc($persona['dni'] ?? '') ?>" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nombres</label>
              <input type="text" class="form-control" value="<?= esc($persona['nombres'] ?? '') ?>" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Apellidos</label>
              <input type="text" class="form-control" value="<?= esc($persona['apellidos'] ?? '') ?>" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo</label>
              <input type="text" class="form-control" value="<?= esc($persona['correo'] ?? '') ?>" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Teléfono</label>
              <input type="text" class="form-control" value="<?= esc($persona['telefono'] ?? '') ?>" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Dirección</label>
              <input type="text" class="form-control" value="<?= esc($persona['direccion'] ?? '') ?>" readonly>
            </div>
          </div>

          <input type="hidden" name="idpersona" value="<?= $persona['idpersona'] ?>">

          <!-- Origen -->
          <div class="mb-3">
            <label for="origenSelect" class="form-label">Origen</label>
            <select id="origenSelect" name="idorigen" class="form-select">
              <option value="">Selecciona origen</option>
              <?php foreach ($origenes as $origen): ?>
                <option value="<?= $origen['idorigen'] ?>" data-tipo="<?= strtolower($origen['nombre']) ?>">
                  <?= $origen['nombre'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Campaña -->
          <div class="mb-3" id="campaniaDiv" style="display:none;">
            <label for="campaniaSelect" class="form-label">Campaña</label>
            <select id="campaniaSelect" name="idcampania" class="form-select">
              <option value="">Selecciona campaña</option>
              <?php foreach ($campanias as $campana): ?>
                <option value="<?= $campana['idcampania'] ?>"><?= $campana['nombre'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Referido -->
          <div class="mb-3" id="referenteDiv" style="display:none;">
            <label for="referido_por" class="form-label">Referido por</label>
            <input type="text" id="referido_por" name="referido_por" class="form-control" placeholder="Nombre del referente">
          </div>

          <!-- Modalidad -->
          <div class="mb-3">
            <label for="modalidadSelect" class="form-label">Modalidad</label>
            <select id="modalidadSelect" name="idmodalidad" class="form-select">
              <option value="">Selecciona modalidad</option>
              <?php foreach ($modalidades as $mod): ?>
                <option value="<?= $mod['idmodalidad'] ?>"><?= $mod['nombre'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>
