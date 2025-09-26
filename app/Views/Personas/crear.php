<?= $header ?>
<link rel="stylesheet" href="<?= base_url('css/personas.css') ?>"> 

<div class="container-fluid mt-4 custom-container">
  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-10">

      <div class="mb-4 d-flex justify-content-between align-items-center">
          <h3 class="mb-0"><?= isset($persona) ? 'Editar Persona' : 'Registro De Personas' ?></h3>
          <a href="<?= base_url('personas'); ?>" class="btn btn-outline-secondary btn-sm">Lista de personas</a>
      </div>

      <form action="<?= base_url('personas/guardar') ?>" id="form-persona" method="POST" autocomplete="off" class="w-100">
          <div class="card shadow-sm">
              <div class="card-body">

                  <div class="form-group">
                      <label for="dni" class="form-label">Buscar DNI</label>
                      <small class="d-none" id="searching">Por favor espere</small>
                      <div class="input-group">
                          <input type="text" class="form-control" name="dni" id="dni" maxlength="8" minlength="8" required autofocus value="<?= esc($persona['dni'] ?? '') ?>">
                          <button class="btn btn-outline-success" type="button" id="buscar-dni">Buscar</button>
                      </div>
                  </div>

                  <div class="row g-3">
                      <div class="col-md-6 form-group">
                          <label for="apellidos" class="form-label">Apellidos</label>
                          <input type="text" class="form-control" name="apellidos" id="apellidos" required 
                            value="<?= esc($persona['apellidos'] ?? '') ?>" readonly>
                      </div>
                      <div class="col-md-6 form-group">
                          <label for="nombres" class="form-label">Nombres</label>
                          <input type="text" class="form-control" name="nombres" id="nombres" required 
                            value="<?= esc($persona['nombres'] ?? '') ?>" readonly>
                      </div>
                  </div>

                  <div class="row g-3 mt-3">
                      <div class="col-md-8 form-group">
                          <label for="correo" class="form-label">Correo Electrónico</label>
                          <input type="email" class="form-control" name="correo" id="correo" value="<?= esc($persona['correo'] ?? '') ?>">
                      </div>
                      <div class="col-md-4 form-group">
                          <label for="telefono" class="form-label">Teléfono</label>
                          <input type="text" class="form-control" name="telefono" id="telefono" maxlength="9" pattern="[0-9]*" inputmode="numeric" title="Solo se permiten números" required value="<?= esc($persona['telefono'] ?? '') ?>">
                      </div>
                  </div>

                  <div class="row g-3 mt-3">
                      <div class="col-md-8 form-group">
                          <label for="direccion" class="form-label">Dirección</label>
                          <input type="text" class="form-control" name="direccion" id="direccion" value="<?= esc($persona['direccion'] ?? '') ?>">
                      </div>
                      <div class="col-md-4 form-group">
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

                  <div class="row g-3 mt-3">
                      <div class="col-12 form-group">
                          <label for="referencias" class="form-label">Referencia</label>
                          <input type="text" class="form-control" name="referencias" id="referencias" value="<?= esc($persona['referencias'] ?? '') ?>">
                      </div>
                  </div>

                  <input type="hidden" name="idpersona" value="<?= esc($persona['idpersona'] ?? '') ?>">

              </div>
              <div class="card-footer text-end">
                  <button class="btn btn-outline-secondary btn-sm me-2" type="reset">Cancelar</button>
                  <button class="btn btn-primary btn-sm" type="submit">Guardar</button>
              </div>
          </div>
      </form>

    </div>
  </div>
</div>
<div id="modalContainer"></div>
<script>
  const BASE_URL = "<?= rtrim(base_url(), '/') ?>/";
</script>
<script src="<?= base_url('js/personasJS/personas.js') ?>"></script>
<script type="module" src="<?= base_url('js/leadsJS/leadsForm.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?= $footer ?>
