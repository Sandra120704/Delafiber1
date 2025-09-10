<!-- Modal Crear Lead -->
<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <!-- Header -->
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">➕ Registrar Lead</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <form id="leadForm" action="<?= base_url('lead/guardar') ?>" method="POST">

          <!-- Persona -->
          <input type="hidden" name="idpersona" value="<?= $persona['idpersona'] ?? '' ?>">

          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label">DNI:</label>
              <input type="text" class="form-control" value="<?= $persona['dni'] ?? '' ?>" readonly>
            </div>
            <div class="col-md-4">
              <label class="form-label">Nombres:</label>
              <input type="text" class="form-control" value="<?= $persona['nombres'] ?? '' ?>" readonly>
            </div>
            <div class="col-md-4">
              <label class="form-label">Apellidos:</label>
              <input type="text" class="form-control" value="<?= $persona['apellidos'] ?? '' ?>" readonly>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Teléfono:</label>
              <input type="text" class="form-control" value="<?= $persona['telefono'] ?? '' ?>" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo:</label>
              <input type="email" class="form-control" value="<?= $persona['correo'] ?? '' ?>" readonly>
            </div>
          </div>

          <hr>

          <!-- Origen -->
          <div class="mb-3">
            <label class="form-label">Origen:</label>
            <select name="idorigen" id="origenSelect" class="form-control">
              <option value="">-- Seleccionar --</option>
              <?php foreach ($origenes as $o): ?>
                <option value="<?= $o['idorigen'] ?>" data-tipo="<?= strtolower($o['nombre']) ?>">
                  <?= $o['nombre'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Campaña -->
          <div class="mb-3" id="campaniaDiv" style="display:none;">
            <label class="form-label">Campaña:</label>
            <select name="idcampania" id="campaniaSelect" class="form-control">
              <option value="">-- Seleccionar campaña --</option>
              <?php foreach ($campanias as $c): ?>
                <option value="<?= $c['idcampania'] ?>"><?= $c['nombre'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Referido -->
          <div class="mb-3" id="referenteDiv" style="display:none;">
            <label class="form-label">Referido por:</label>
            <input type="text" id="referido_por" name="referido_por" class="form-control" placeholder="Nombre de la persona que refiere">
          </div>

          <!-- Modalidad y Medio -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Modalidad:</label>
              <select name="idmodalidad" class="form-control" required>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($modalidades as $mo): ?>
                  <option value="<?= $mo['idmodalidad'] ?>"><?= $mo['nombre'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Medio:</label>
              <select name="idmedio" class="form-control">
                <option value="">-- Seleccionar --</option>
                <?php foreach ($medios as $m): ?>
                  <option value="<?= $m['idmedio'] ?>"><?= $m['nombre'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Botón -->
          <div class="text-end mt-3">
            <button type="submit" class="btn btn-success">
              <i class="bi bi-save"></i> Guardar Lead
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<!-- Script cascada -->
<script>
document.addEventListener('DOMContentLoaded', function(){
    const origenSelect = document.getElementById('origenSelect');
    const campaniaDiv = document.getElementById('campaniaDiv');
    const referenteDiv = document.getElementById('referenteDiv');
    const campSelect = document.getElementById('campaniaSelect');
    const referidoInput = document.getElementById('referido_por');

    function actualizarDivs() {
        if (!origenSelect) return;
        const selectedOption = origenSelect.options[origenSelect.selectedIndex];
        const tipo = selectedOption.getAttribute('data-tipo') || '';

        if (tipo === 'campaña') {
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
#leadModal .modal-content { border-radius: 12px; }
#leadModal .modal-header { border-bottom: 1px solid #ddd; }
#leadModal .form-label { font-weight: 600; }
</style>
