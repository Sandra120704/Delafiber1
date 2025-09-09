<?= $header ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/leads.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>📋 Flujo de Leads</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#leadModal">Nuevo Lead</button>
</div>

<div class="kanban-container">
    <div class="kanban-header">
        <?php foreach ($etapas as $etapa): ?>
            <div class="kanban-stage"><?= htmlspecialchars($etapa['nombre']) ?></div>
        <?php endforeach; ?>
    </div>

    <div class="kanban-body">
        <?php foreach ($etapas as $etapa): ?>
            <?php
                $leadsEtapa = $leadsPorEtapa[$etapa['idetapa']] ?? [];
            ?>
            <div class="kanban-column" id="kanban-column-<?= $etapa['idetapa'] ?>" data-etapa="<?= $etapa['idetapa'] ?>">
                <?php foreach ($leadsEtapa as $lead): ?>
                    <div class="kanban-card"
                         id="kanban-card-<?= $lead['idlead'] ?>"
                         data-id="<?= $lead['idlead'] ?>"
                         style="border-left: 5px solid <?= htmlspecialchars($lead['estatus_color'] ?? '#007bff') ?>;"
                         draggable="true">
                        <div class="card-title"><?= htmlspecialchars($lead['nombres'] . ' ' . $lead['apellidos']) ?></div>
                        <div class="card-info">
                            <?= htmlspecialchars($lead['telefono']) ?><br>
                            <?= htmlspecialchars($lead['correo']) ?><br>
                            <?= htmlspecialchars($lead['campania'] ?? '') ?> - <?= htmlspecialchars($lead['medio'] ?? '') ?><br>
                            Usuario: <?= htmlspecialchars($lead['usuario']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="modalContainer"></div>

<!-- Modal Crear Lead -->
<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registrar Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="leadForm" action="<?= base_url('lead/guardar') ?>" method="POST">
          <input type="hidden" name="idpersona" id="idpersona" value="">

          <div class="mb-2">
            <label>DNI:</label>
            <input type="text" id="dni" class="form-control" placeholder="DNI">
            <button type="button" id="btnBuscarDni" class="btn btn-primary mt-1">Buscar</button>
          </div>

          <div class="mb-2">
            <label>Nombres:</label>
            <input type="text" id="nombres" name="nombres" class="form-control">
          </div>

          <div class="mb-2">
            <label>Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" class="form-control">
          </div>

          <div class="mb-2">
            <label>Teléfono:</label>
            <input type="text" id="telefono" name="telefono" class="form-control">
          </div>

          <div class="mb-2">
            <label>Correo:</label>
            <input type="email" id="correo" name="correo" class="form-control">
          </div>

          <button type="submit" class="btn btn-success">Registrar Lead</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="modalLeadDetalleContainer"></div>


<?= $footer ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const base_url = "<?= base_url() ?>";
</script>
<script src="<?= base_url('js/leads.js') ?>"></script>
