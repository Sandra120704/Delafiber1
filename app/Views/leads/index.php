<?= $header ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/leads.css') ?>">
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>📋 Flujo de Leads</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#leadModal">Nuevo Lead</button>
</div>

<!-- Kanban Container -->
<div class="kanban-container">
  <?php foreach ($etapas as $etapa): ?>
    <div class="kanban-column" id="kanban-column-<?= $etapa['idetapa'] ?>" data-etapa="<?= $etapa['idetapa'] ?>">
        <div class="kanban-stage"><?= htmlspecialchars($etapa['nombre']) ?></div>
        <?php
          $leadsEtapa = $leadsPorEtapa[$etapa['idetapa']] ?? [];
        ?>
        <?php foreach ($leadsEtapa as $lead): ?>
          <div class="kanban-card" id="kanban-card-<?= $lead['idlead'] ?>" data-id="<?= $lead['idlead'] ?>" draggable="true" style="border-left:5px solid <?= htmlspecialchars($lead['estatus_color'] ?? '#007bff') ?>;">
            <div class="card-title"><?= htmlspecialchars($lead['nombres'].' '.$lead['apellidos']) ?></div>
            <div class="card-info">
              <small><?= htmlspecialchars($lead['telefono']) ?> | <?= htmlspecialchars($lead['correo']) ?></small><br>
              <small><?= htmlspecialchars($lead['campania'] ?? '') ?> - <?= htmlspecialchars($lead['medio'] ?? '') ?></small><br>
              <small>Usuario: <?= htmlspecialchars($lead['usuario']) ?></small>
            </div>
          </div>
        <?php endforeach; ?>
        <button class="btn btn-sm btn-outline-primary mt-2" onclick="abrirModalAgregarLead(<?= $etapa['idetapa'] ?>)">+ Agregar Lead</button>
    </div>
  <?php endforeach; ?>
</div>

<!-- Modal Crear Lead -->
<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registrar Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="leadForm">
          <input type="hidden" id="idpersona" name="idpersona" value="">
          <input type="hidden" id="idetapa" name="idetapa" value="1">

          <div class="mb-2"><label>DNI:</label><input type="text" id="dni" class="form-control" placeholder="DNI"></div>
          <div class="mb-2"><label>Nombres:</label><input type="text" id="nombres" name="nombres" class="form-control"></div>
          <div class="mb-2"><label>Apellidos:</label><input type="text" id="apellidos" name="apellidos" class="form-control"></div>
          <div class="mb-2"><label>Teléfono:</label><input type="text" id="telefono" name="telefono" class="form-control"></div>
          <div class="mb-2"><label>Correo:</label><input type="email" id="correo" name="correo" class="form-control"></div>

          <button type="submit" class="btn btn-success">Registrar Lead</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $footer ?>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
const base_url = "<?= base_url() ?>";

// Abrir modal y definir etapa
function abrirModalAgregarLead(idetapa){
    $('#idetapa').val(idetapa);
    const modal = new bootstrap.Modal(document.getElementById('leadModal'));
    modal.show();
}

// Drag & Drop funcional
function enableDragAndDrop(){
    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('dragstart', e => {
            e.dataTransfer.setData('text/plain', card.dataset.id);
        });
    });

    document.querySelectorAll('.kanban-column').forEach(col => {
        col.addEventListener('dragover', e => e.preventDefault());
        col.addEventListener('drop', e => {
            const id = e.dataTransfer.getData('text/plain');
            const card = document.getElementById(`kanban-card-${id}`);
            col.appendChild(card);

            // Actualizar etapa vía AJAX
            $.post(`${base_url}/lead/mover`, { idlead: id, idetapa: col.dataset.etapa }, function(res){
                Swal.fire({
                    icon: res.status === 'success' ? 'success' : 'error',
                    title: res.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
            }, "json");
        });
    });
}

// Inicializar drag & drop al cargar
enableDragAndDrop();

// Registrar lead vía AJAX
$('#leadForm').on('submit', function(e){
    e.preventDefault();
    $.post(`${base_url}/lead/guardar`, $(this).serialize(), function(res){
        if(res.status === 'success'){
            const column = $('#kanban-column-' + $('#idetapa').val());
            const leadCard = $(`
                <div class="kanban-card" id="kanban-card-${res.idlead}" data-id="${res.idlead}" draggable="true" style="border-left:5px solid #007bff;">
                    <div class="card-title">${$('#nombres').val()} ${$('#apellidos').val()}</div>
                    <div class="card-info">
                        <small>${$('#telefono').val()} | ${$('#correo').val()}</small>
                    </div>
                </div>
            `);
            column.append(leadCard);

            // Habilitar drag & drop en la nueva tarjeta
            enableDragAndDrop();

            Swal.fire({
                icon: 'success',
                title: 'Lead registrado',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });

            $('#leadForm')[0].reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById('leadModal'));
            modal.hide();
        } else {
            Swal.fire({
                icon: 'warning',
                title: res.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    }, "json");
});
</script>

<style>
/* Kanban container: columnas paralelas */
.kanban-container {
  display: flex !important;      /* columnas en fila */
  flex-wrap: nowrap !important;  /* no se envuelvan */
  gap: 16px;
  overflow-x: auto;              /* scroll horizontal si hay muchas columnas */
  width: 100%;                   /* ocupar todo el ancho */
  padding: 10px;
}

.kanban-column {
  flex: 0 0 300px;               /* ancho fijo para cada columna */
  background: #f4f5f7;
  border-radius: 8px;
  max-height: 80vh;              /* altura máxima de columna */
  display: flex;
  flex-direction: column;
  padding: 8px;
  overflow-y: auto;              /* scroll vertical si hay muchas tarjetas */
}
/* Nombre de etapa */
.kanban-stage { 
  font-weight:bold; 
  text-align:center; 
  margin-bottom:8px; 
}

/* Tarjetas */
.kanban-card {
  background:#fff; 
  border-radius:6px; 
  padding:10px; 
  margin-bottom:10px; 
  box-shadow:0 2px 5px rgba(0,0,0,0.1); 
  cursor:grab; 
  transition:transform .2s;
}
.kanban-card:hover { transform: scale(1.02); }

/* Info dentro de la tarjeta */
.kanban-card .card-title {
  font-weight:600;
  margin-bottom:4px;
}
.kanban-card .card-info small {
  color:#555;
  display:block;
}
</style>
