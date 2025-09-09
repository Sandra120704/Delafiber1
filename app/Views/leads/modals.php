<!-- Modal Lead Profesional -->
<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title mb-0">
          <?= isset($persona) ? '' . esc($persona['nombres']) : 'Nuevo Lead' ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <?php if(isset($persona) && !empty($persona)): ?>
          <!-- Información del Cliente -->
          <div class="card mb-3 shadow-sm">
            <div class="card-body">
              <div class="row g-2">
                <div class="col-md-6"><strong>Nombre:</strong><br><?= esc($persona['nombres'] . ' ' . $persona['apellidos']) ?></div>
                <div class="col-md-6"><strong>DNI:</strong><br><?= esc($persona['dni'] ?? '-') ?></div>
                <div class="col-md-6"><strong>Teléfono:</strong><br><?= esc($persona['telefono'] ?? '-') ?></div>
                <div class="col-md-6"><strong>Correo:</strong><br><?= esc($persona['correo'] ?? '-') ?></div>
                <div class="col-12"><strong>Dirección:</strong><br><?= esc($persona['direccion'] ?? '-') ?></div>
              </div>
            </div>
          </div>

          <!-- Formulario Lead -->
          <form id="formLead" action="<?= site_url('leads/guardar') ?>" method="post">
            <input type="hidden" name="idpersona" value="<?= esc($persona['idpersona']) ?>">

            <div class="row g-3">
              <!-- Origen -->
              <div class="col-md-6">
                <label for="origenSelect" class="form-label">Origen</label>
                <select name="idorigen" id="origenSelect" class="form-control" required>
                  <option value="">Seleccione origen</option>
                  <?php foreach($origenes as $origen): ?>
                    <option value="<?= $origen['idorigen'] ?>" data-tipo="<?= esc($origen['tipo'] ?? '') ?>">
                      <?= esc($origen['nombre']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Medio -->
              <div class="col-md-6">
                <label for="medioSelect" class="form-label">Medio</label>
                <select name="idmedio" id="medioSelect" class="form-control" required>
                  <option value="">Seleccione medio</option>
                  <?php foreach($medios as $m): ?>
                    <option value="<?= $m['idmedio'] ?>"><?= esc($m['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Campaña -->
              <div class="col-md-6" id="campaniaDiv" style="display:none;">
                <label for="campaniaSelect" class="form-label">Campaña</label>
                <select name="idcampania" id="campaniaSelect" class="form-control">
                  <option value="">Seleccione campaña</option>
                  <?php foreach($campanas as $c): ?>
                    <option value="<?= $c['idcampania'] ?>"><?= esc($c['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Referido -->
              <div class="col-md-6" id="referenteDiv" style="display:none;">
                <label for="referido_por" class="form-label">Referido por</label>
                <input type="text" name="referido_por" id="referido_por" class="form-control">
              </div>

              <!-- Modalidad -->
              <div class="col-md-6">
                <label for="modalidadSelect" class="form-label">Modalidad</label>
                <select name="idmodalidad" id="modalidadSelect" class="form-control" required>
                  <option value="">Seleccione modalidad</option>
                  <?php foreach($modalidades as $m): ?>
                    <option value="<?= $m['idmodalidad'] ?>"><?= esc($m['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

            </div>
          </form>

        <?php else: ?>
          <div class="text-center py-3 text-muted small">No se encontró información del cliente.</div>
        <?php endif; ?>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <?php if(isset($persona) && !empty($persona)): ?>
          <button type="submit" class="btn btn-primary" form="formLead">Guardar Lead</button>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<!-- Script para mostrar/ocultar campos según origen -->
<script>
document.addEventListener('DOMContentLoaded', function(){
    const origenSelect = document.getElementById('origenSelect');
    const campaniaDiv = document.getElementById('campaniaDiv');
    const referenteDiv = document.getElementById('referenteDiv');

    function actualizarDivs() {
        if (!origenSelect) return;
        const selectedOption = origenSelect.options[origenSelect.selectedIndex];
        const tipo = selectedOption.getAttribute('data-tipo') || '';
        const campSelect = document.getElementById('campaniaSelect');
        const referidoInput = document.getElementById('referido_por');

        if (tipo === 'campania') {
            campaniaDiv.style.display = 'block';
            referenteDiv.style.display = 'none';
            campSelect.required = true;
            referidoInput.required = false;
        } else if (tipo === 'referido') {
            campaniaDiv.style.display = 'none';
            referenteDiv.style.display = 'block';
            campSelect.required = false;
            referidoInput.required = true;
        } else {
            campaniaDiv.style.display = 'none';
            referenteDiv.style.display = 'none';
            campSelect.required = false;
            referidoInput.required = false;
        }
    }

    origenSelect.addEventListener('change', actualizarDivs);
    actualizarDivs();
});
</script>

<style>
#leadModal .modal-content { border-radius: 12px; overflow: hidden; }
#leadModal .card { border-radius: 10px; padding: 12px; }
#leadModal .form-label { font-weight: 500; }
#leadModal .modal-footer .btn-primary { background-color: #c43030ff; border: none; }
</style>
