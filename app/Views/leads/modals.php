<!-- Modal Crear Lead -->
<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registrar Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="leadForm" action="<?= base_url('leads/guardar') ?>" method="POST">
          
          <!-- Persona -->
          <input type="hidden" name="idpersona" id="idpersona" value="<?= $persona['idpersona'] ?? '' ?>">

          <div class="row">
            <div class="col-md-4 mb-2">
              <label>DNI:</label>
              <input type="text" name="dni" id="dni" class="form-control" value="<?= $persona['dni'] ?? '' ?>" readonly>
            </div>
            <div class="col-md-4 mb-2">
              <label>Nombres:</label>
              <input type="text" name="nombres" id="nombres" class="form-control" value="<?= $persona['nombres'] ?? '' ?>" readonly>
            </div>
            <div class="col-md-4 mb-2">
              <label>Apellidos:</label>
              <input type="text" name="apellidos" id="apellidos" class="form-control" value="<?= $persona['apellidos'] ?? '' ?>" readonly>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-2">
              <label>Teléfono:</label>
              <input type="text" name="telefono" id="telefono" class="form-control" value="<?= $persona['telefono'] ?? '' ?>" readonly>
            </div>
            <div class="col-md-6 mb-2">
              <label>Correo:</label>
              <input type="email" name="correo" id="correo" class="form-control" value="<?= $persona['correo'] ?? '' ?>" readonly>
            </div>
          </div>

          <hr>

          <!-- Datos Lead -->
          <div class="row">
            <div class="col-md-6 mb-2">
              <label>Difusión:</label>
              <select name="iddifusion" class="form-control" required>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($difusiones as $d): ?>
                  <option value="<?= $d['iddifusion'] ?>">
                    <?= $d['campania_nombre'] ?> - <?= $d['medio_nombre'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6 mb-2">
              <label>Modalidad:</label>
              <select name="idmodalidad" class="form-control" required>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($modalidades as $mo): ?>
                  <option value="<?= $mo['idmodalidad'] ?>"><?= $mo['nombre'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6 mb-2">
              <label>Origen:</label>
              <select name="idorigen" id="origenSelect" class="form-control">
                <option value="">-- Seleccionar --</option>
                <?php foreach ($origenes as $o): ?>
                  <option value="<?= $o['idorigen'] ?>" 
                          data-tipo="<?= strtolower(str_replace(['á','é','í','ó','ú'], ['a','e','i','o','u'], $o['nombre'])) ?>">
                    <?= $o['nombre'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="mb-2" id="referenteDiv" style="display:none;">
            <label>Referido por:</label>
            <input type="text" id="referido_por" name="referido_por" class="form-control" placeholder="Nombre de la persona que refiere">
          </div>

          <div class="mt-3 text-end">
            <button type="submit" class="btn btn-success">Registrar Lead</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- JS externo -->
<script src="<?= base_url('js/leadsJS/leadsForm.js') ?>"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
#leadModal .modal-content { border-radius: 12px; }
#leadModal .modal-header { border-bottom: 1px solid #ddd; }
#leadModal .form-label { font-weight: 600; }
</style>
