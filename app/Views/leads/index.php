<?= $header ?>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="<?= base_url('css/leads.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>📋 Flujo de Leads</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#leadModal">
        ➕ Nuevo Lead
    </button>
</div>

<div class="kanban-container">
    <!-- Cabecera con etapas -->
    <div class="kanban-header">
        <?php foreach ($etapas as $etapa): ?>
            <div class="kanban-stage">
                <?= htmlspecialchars($etapa['nombre']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Cuerpo con columnas y tarjetas -->
    <div class="kanban-body">
        <?php foreach ($etapas as $etapa): ?>
            <?php
            // Si no hay leads para esta etapa, la saltamos
            if (empty($leadsPorEtapa[$etapa['idetapa']])) continue;

            $leadsEtapa = $leadsPorEtapa[$etapa['idetapa']];
            ?>
            <div class="kanban-column"
                 id="kanban-column-<?= $etapa['idetapa'] ?>"
                 data-etapa="<?= $etapa['idetapa'] ?>">

                <?php foreach ($leadsEtapa as $lead): ?>
                    <div class="kanban-card"
                         id="kanban-card-<?= $lead['idlead'] ?>"
                         data-id="<?= $lead['idlead'] ?>"
                         style="border-left: 5px solid <?= htmlspecialchars($lead['estatus_color'] ?? '#007bff') ?>;"
                         draggable="true">

                        <div class="card-title">
                            <?= htmlspecialchars($lead['nombres'] . ' ' . $lead['apellidos']) ?>
                        </div>
                        <div class="card-info">
                            <?= htmlspecialchars($lead['telefono']) ?><br>
                            <?= htmlspecialchars($lead['correo']) ?><br>
                            <?= htmlspecialchars($lead['campana'] ?? '') ?> - <?= htmlspecialchars($lead['medio'] ?? '') ?><br>
                            Usuario: <?= htmlspecialchars($lead['usuario']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Contenedor para modales dinámicos -->
<div id="modalContainer"></div>

<div class="modal fade" id="leadModal" tabindex="-1" aria-labelledby="leadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="leadModalLabel">➕ Nuevo Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form id="leadForm" action="<?= base_url('lead/guardar') ?>" method="post">
        <div class="modal-body">
          <div class="row g-3">

            <!-- DNI + botón buscar -->
            <div class="col-md-4">
              <label for="dni" class="form-label">DNI</label>
              <div class="input-group">
                <input type="text" class="form-control" name="dni" id="dni" maxlength="8" required>
                <button class="btn btn-outline-secondary" type="button" id="btnBuscarDni">Buscar</button>
              </div>
            </div>

            <!-- Datos de persona -->
            <div class="col-md-4">
              <label for="nombres" class="form-label">Nombres</label>
              <input type="text" class="form-control" name="nombres" id="nombres" required>
            </div>

            <div class="col-md-4">
              <label for="apellidos" class="form-label">Apellidos</label>
              <input type="text" class="form-control" name="apellidos" id="apellidos" required>
            </div>

            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="text" class="form-control" name="telefono" id="telefono">
            </div>

            <div class="col-md-6">
              <label for="correo" class="form-label">Correo</label>
              <input type="email" class="form-control" name="correo" id="correo">
            </div>

            <!-- Selección de campaña / medio / etapa -->
            <div class="col-md-4">
              <label for="idcampania" class="form-label">Campaña</label>
              <select name="idcampania" id="idcampania" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php foreach ($campanias as $c): ?>
                  <option value="<?= $c['idcampania'] ?>"><?= $c['nombre'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-4">
              <label for="idmedio" class="form-label">Medio</label>
              <select name="idmedio" id="idmedio" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php foreach ($medios as $m): ?>
                  <option value="<?= $m['idmedio'] ?>"><?= $m['nombre'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-4">
              <label for="idetapa" class="form-label">Etapa inicial</label>
              <select name="idetapa" id="idetapa" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php foreach ($etapas as $e): ?>
                  <option value="<?= $e['idetapa'] ?>"><?= $e['nombre'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar Lead</button>
        </div>
      </form>

    </div>
  </div>
</div>



<?= $footer ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const base_url = "<?= base_url() ?>";
</script>
<script src="<?= base_url('js/leads.js') ?>"></script>
