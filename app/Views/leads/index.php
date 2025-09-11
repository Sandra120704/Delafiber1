<?= $header ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/leads.css') ?>">
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Flujo de Leads</h3>
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

<!-- Modal Detalle -->
<div class="modal fade" id="modalLeadDetalle" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="modalLeadDetalleContent"></div>
  </div>
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

<script>
    const base_url = "<?= rtrim(base_url(), '/') ?>";
</script>
<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('js/leadsJS/kanbas.js') ?>"></script>
<script src="<?= base_url('js/leadsJS/editar.js') ?>"></script>
<script src="<?= base_url('js/leadsJS/detalle.js') ?>"></script>
<script type="module" src="<?= base_url('js/leadsJS/leadsForm.js') ?>"></script>
<style>
/* Kanban Container */
.kanban-container { display:flex; flex-wrap:nowrap; gap:16px; overflow-x:auto; width:100%; padding:10px; }
.kanban-column { flex:0 0 300px; background:#f4f5f7; border-radius:8px; max-height:80vh; display:flex; flex-direction:column; padding:8px; overflow-y:auto; }
.kanban-stage { font-weight:bold; text-align:center; margin-bottom:8px; }
.kanban-card { background:#fff; border-radius:6px; padding:10px; margin-bottom:10px; box-shadow:0 2px 5px rgba(0,0,0,0.1); cursor:grab; transition:transform .2s; }
.kanban-card:hover { transform: scale(1.02); }
.kanban-card .card-title { font-weight:600; margin-bottom:4px; }
.kanban-card .card-info small { color:#555; display:block; }
</style>
